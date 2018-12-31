<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationComplete extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $reservation;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $reservation
     */
    public function __construct($user, $reservation)
    {
        $this->user = $user;
        $this->reservation = $reservation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Reservation Confirmed')
            ->view('email.reservation-confirmation', [
                'reservation' => $this->reservation,
                'user' => $this->user,
                'property' => $this->reservation->property
            ]);
    }
}
