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

        $this->assertContains($formData['name'], $rendered);
        $this->assertContains($formData['email'], $rendered);
        $this->assertContains($formData['phone'], $rendered);
        $this->assertContains($formData['message'], $rendered);
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
