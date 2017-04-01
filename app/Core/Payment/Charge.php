<?php

namespace App\Core\Payment;

class Charge
{
    protected $charge;
    protected $id;
    protected $expMonth;
    protected $expYear;
    protected $lastFour;
    protected $amount;
    protected $brand;

    /**
     * Charge constructor.
     * @param $charge
     */
    function __construct(array $charge)
    {
        $this->charge = $charge;
        $this->id = $charge['id'];
        $this->expMonth = $charge['exp_month'];
        $this->expYear = $charge['exp_year'];
        $this->lastFour = $charge['last_four'];
        $this->amount = $charge['amount'];
        $this->brand = $charge['brand'];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getCardLastFour()
    {
        return $this->lastFour;
    }

    /**
     * @return mixed
     */
    public function getExpMonth()
    {
        return $this->expMonth;
    }

    /**
     * @return mixed
     */
    public function getExpYear()
    {
        return $this->expYear;
    }

    /**
     * @return mixed
     */
    public function getCardBrand()
    {
        return $this->brand;
    }
}