<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    protected $config;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $config)
    {
        $this->user = $user;
        $this->config = $config;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.registration')
        ->with([
            'user' => $this->user
        ]);
    }
}
