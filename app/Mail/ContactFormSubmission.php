<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmission extends Mailable
{
    use Queueable, SerializesModels;

    protected $formData;
    protected $config;

    /**
     * Create a new message instance.
     *
     */
    public function __construct($formData, $config)
    {
        $this->formData = $formData;
        $this->config = $config;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->config['accounts']['admin']['to'])
            ->from([
                'address' => $this->config['from']['address'],
                'name' => $this->config['from']['name']
            ])
            ->replyTo($this->formData['email'])
            ->view('email.contact-form-submission')
            ->with([
                'data' => $this->formData,
            ]);
    }
}
