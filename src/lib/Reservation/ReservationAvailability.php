<?php namespace bookMe\lib\Reservation;

use bookMe\Model\Reservation;

/**
 * Class ReservationAvailability
 *
 * This class contains the method used to check if a property is available.
 *
 * This class has 1 dependency, an instance of the Reservation class.
 * The ReservationAvailability class contains 1 public method: check.
 * The check method will return a boolean value indicating the availability of a property.
 *
 *
 * PHP Version 5.6
 *
 * License: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package bookMe
 * @author Justin Christenson <info@justinc.me>
 * @version 1.0.0
 * @license http://opensource.org/licenses/mit-license.php
 * @link http://bookme.justinc.me
 *
 */

class ReservationAvailability implements ReservationAvailabilityInterface {

    protected $_reservation;

    public function __construct(Reservation $reservation)
    {
        $this->_reservation = $reservation;
    }

    /**
     * Check if property is available for the given date range.
     *
     * @return bool
     */
    public function check()
    {
        $pid = $this->_reservation->pid;
        $checkIn = $this->_reservation->check_in;
        $checkOut = $this->_reservation->check_out;
        if($this->_reservation->checkDateConflicts($pid, $checkIn, $checkOut)->isEmpty())
        {
            return true;
        }
        return false;
    }
}