<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmission extends Mailable
{
    use Queueable, SerializesModels;

    protected $formData;

    /**
     * Create a new message instance.
     *
     */
    public function __construct($formData)
    {
        $this->formData = $formData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Contact Request')
            ->replyTo($this->formData['email'])
            ->view('email.contact-form-submission', [
                'data' => $this->formData,
            ]);
    }
}
