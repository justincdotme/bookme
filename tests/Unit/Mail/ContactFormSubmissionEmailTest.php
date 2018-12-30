<?php

namespace Tests\Unit\Mail;

use App\Mail\ContactFormSubmission;
use Tests\TestCase;

class ContactFormSubmissionEmailTest extends TestCase
{
    /**
     * @test
     */
    public function email_contains_form_data()
    {
        $formData = [
            'name' => 'Foo McBar',
            'email' => 'foo@bar.baz',
            'phone' => '123-456-7890',
            'message' => 'This is just a test.'
        ];
        $email = new ContactFormSubmission($formData);

        $rendered = $this->renderMailable($email);

        $this->assertContains('<h1>Contact Request From Foo McBar</h1>', $rendered);
        $this->assertContains('<strong>Email: </strong>foo@bar.baz', $rendered);
        $this->assertContains('<strong>Phone: </strong>123-456-7890', $rendered);
        $this->assertContains('<strong>Message: </strong>This is just a test.', $rendered);
    }

    /**
     * @test
     */
    public function email_has_subject()
    {
        $email = new ContactFormSubmission([
            'name' => '...',
            'email' => '...',
            'phone' => '...',
            'message' => '...'
        ]);

        $this->assertEquals('New Contact Request', $email->build()->subject);
    }
}
