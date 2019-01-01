<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationCancelled extends Mailable implements ShouldQueue
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
        return $this->subject('Reservation Cancellation Notice')
            ->view('email.reservation-cancellation-notice', [
            'reservation' => $this->reservation,
            'user' => $this->user,
        ]);
    }
}
