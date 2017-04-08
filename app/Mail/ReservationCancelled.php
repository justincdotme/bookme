<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationCancelled extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $reservation;
    protected $config;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $reservation
     * @param $config
     */
    public function __construct($user, $reservation, $config)
    {
        $this->user = $user;
        $this->reservation = $reservation;
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
            ->view('email.reservation-cancellation-notice')
            ->with([
                'reservation' => $this->reservation,
                'user' => $this->user,
            ]);
    }
}
