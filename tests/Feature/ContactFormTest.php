<?php

namespace Tests\Feature;

use Tests\TestCase;
use EmailTestHelpers;
use Illuminate\Support\Facades\Mail;
use TestingMailEventListener;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactFormTest extends TestCase
{
    use DatabaseMigrations;
    use EmailTestHelpers;

    public function setUp($name = null, array $data = [], $dataName = '')
    {
        parent::setUp($name, $data, $dataName);
        Mail::getSwiftMailer()
            ->registerPlugin(new TestingMailEventListener($this));
    }

    /**
     * @test
     */
    public function user_can_view_contact_form()
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function user_can_submit_contact_form()
    {
        $response = $this->post("/contact", [
            'name' => 'foo bar baz',
            'email' => 'foo@bar.baz',
            'phone' => 1231231231,
            'message' => 'This is a test message.'
        ]);

        $this->seeEmailWasSent();
        $this->seeEmailsSent(2);
        $this->seeEmailTo(config('mail.accounts.admin.to'), $this->emails[0]);
        $this->seeEmailFrom('no-reply@bookme.justinc.me');
        $this->seeEmailContains("Contact Request From foo bar baz", $this->emails[0]);
        $this->seeEmailContains("1231231231", $this->emails[0]);
        $this->seeEmailContains("foo@bar.baz", $this->emails[0]);
        $this->seeEmailContains("This is a test message.", $this->emails[0]);
        $this->seeEmailTo("foo@bar.baz", $this->emails[1]);
        $this->seeEmailFrom('no-reply@bookme.justinc.me', $this->emails[1]);
        $this->seeEmailContains("Thank You, foo bar baz", $this->emails[1]);
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function contact_form_submission_is_limited_to_one_per_session()
    {
        $formSubmission1 = $this->post("/contact", [
            'name' => 'foo bar baz',
            'email' => 'foo@bar.baz',
            'phone' => 1231231231,
            'message' => 'This is a test message.'
        ]);

        $formSubmission2 = $this->post("/contact", [
            'name' => 'foo bar baz',
            'email' => 'foo@bar.baz',
            'phone' => 1231231231,
            'message' => 'This is a test message.'
        ]);

        $formSubmission2->assertStatus(422);
    }

    /**
     * @test
     */
    public function name_is_required_to_update_standard_user()
    {
        $this->response = $this->post("/contact", [
            'email' => 'foo@bar.baz',
            'phone' => 1231231231,
            'message' => 'This is a test message.'
        ]);

        $this->assertFieldHasValidationError('name');
    }

    /**
     * @test
     */
    public function email_is_required_to_update_standard_user()
    {
        $this->response = $this->post("/contact", [
            'name' => 'foo bar baz',
            'phone' => 1231231231,
            'message' => 'This is a test message.'
        ]);

        $this->assertFieldHasValidationError('email');
    }

    /**
     * @test
     */
    public function phone_is_required_to_update_standard_user()
    {
        $this->response = $this->post("/contact", [
            'name' => 'foo bar baz',
            'email' => 'foo@bar.baz',
            'message' => 'This is a test message.'
        ]);

        $this->assertFieldHasValidationError('phone');
    }

    /**
     * @test
     */
    public function phone_must_be_numeric_to_update_standard_user()
    {
        $this->response = $this->post("/contact", [
            'name' => 'foo bar baz',
            'email' => 'foo@bar.baz',
            'phone' => "abc123abc",
            'message' => 'This is a test message.'
        ]);

        $this->assertFieldHasValidationError('phone');
    }

    /**
     * @test
     */
    public function message_is_required_to_update_standard_user()
    {
        $this->response = $this->post("/contact", [
            'name' => 'foo bar baz',
            'email' => 'foo@bar.baz',
            'phone' => 1231231231
        ]);

        $this->assertFieldHasValidationError('message');
    }
}
