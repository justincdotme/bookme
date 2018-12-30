<?php

namespace Tests\Feature;

use App\Mail\ContactFormConfirmation;
use App\Mail\ContactFormSubmission;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ContactFormTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp($name = null, array $data = [], $dataName = '')
    {
        parent::setUp($name, $data, $dataName);
        Mail::fake();
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
        $response = $this->submit_contact_form([
            'name' => 'foo bar baz',
            'email' => 'foo@bar.baz',
            'phone' => 1231231231,
            'message' => 'This is a test message.'
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function contact_form_submission_sends_notification_email_to_admin()
    {
        //TODO - Email Queue test
        $this->submit_contact_form([
            'name' => 'foo bar baz',
            'email' => 'foo@bar.baz',
            'phone' => 1231231231,
            'message' => 'This is a test message.'
        ]);

        Mail::assertSent(ContactFormSubmission::class, function ($mail) {
            return $mail->hasTo(config('mail.accounts.admin.to'));
        });
    }

    /**
     * @test
     */
    public function contact_form_submission_sends_confirmation_email_to_user()
    {
        //TODO - Email Queue test
        $this->submit_contact_form([
            'name' => 'foo bar baz',
            'email' => 'foo@bar.baz',
            'phone' => 1231231231,
            'message' => 'This is a test message.'
        ]);

        Mail::assertSent(ContactFormConfirmation::class, function ($mail) {
            return $mail->hasTo('foo@bar.baz');
        });
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

    protected function submit_contact_form(array $data)
    {
        return $this->post("/contact", $data);
    }
}
