<?php namespace bookMe\Model;

class Weather {

    protected $_weatherData;
    protected $_temperature;

    public function __construct($weatherData)
    {
        $this->_weatherData = $weatherData;
        if(!is_null($this->_weatherData))
        {
            $this->_temperature = htmlentities($this->_weatherData->main->temp);
        }
    }

    /**
     * Return all weather data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->_weatherData;
    }

    /**
     * Return temperature.
     *
     * @return float
     */
    public function getTemp()
    {
        if(is_null($this->_temperature))
        {
            return $this->_temperature;
        }
        return round($this->kelvinToFahrenheit($this->_temperature));
    }

    /**
     * Convert the temperature from Kelvin to Farenheight.
     *
     * @param $temp
     * @return float
     */
    protected function kelvinToFahrenheit($temp)
    {
        return ($temp - 273.15) * 9 / 5 + 32;
    }
}