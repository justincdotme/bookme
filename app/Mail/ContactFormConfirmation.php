<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactFormConfirmation extends Mailable
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
        return $this->to($this->formData['email'])
            ->from([
                'address' => $this->config['from']['address'],
                'name' => $this->config['from']['name']
            ])
            ->replyTo($this->config['accounts']['admin']['to'])
            ->view('email.contact-form-confirmation')
            ->with([
                'data' => $this->formData,
            ]);
    }
}
