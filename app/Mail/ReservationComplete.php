<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReservationComplete extends Mailable
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
        return $this->from([
                'address' => $this->config['from']['address'],
                'name' => $this->config['from']['name']
            ])
            ->view('email.reservation-confirmation')
            ->with([
                'reservation' => $this->reservation,
                'user' => $this->user,
                'property' => $this->reservation->property
            ]);
    }
}
