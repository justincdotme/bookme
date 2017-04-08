<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegistration extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
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
        return $this->to($this->user)
            ->from([
            'address' => $this->config['from']['address'],
            'name' => $this->config['from']['name']
        ])
        ->view('email.registration')
        ->with([
            'user' => $this->user
        ]);
    }
}
