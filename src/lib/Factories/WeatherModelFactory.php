<?php namespace bookMe\lib\Factories;

use bookMe\lib\DataAccess\RemoteDataInterface;
use bookMe\lib\Http\Session;
use bookMe\Model\Weather;
use Exception;

/**
 * Class WeatherModelFactory
 *
 * This class is responsible creating an instance of the Weather model.
 *
 * This class has 1 public method, make, which accepts an instance of a class implementing the RemoteData Interface.
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
 * @link https://bookme.justinc.me
 *
 */

class WeatherModelFactory {

    /**
     * Create a new Weather model with data.
     *
     * @param RemoteDataInterface $remote
     * @return Weather
     * @throws Exception
     */
    public static function make(RemoteDataInterface $remote)
    {
        $weatherData = self::getData($remote);
        return new Weather($weatherData);
    }

    /**
     * Get weather data from the session cache.
     *
     * @param $remote
     * @return mixed
     */
    protected static function getData($remote)
    {
        if(!Session::has('weather-data'))
        {
            $weatherData = $remote->getData('http://api.openweathermap.org/data/2.5/weather?zip=' . ZIP_CODE . ',us', null);
            $sessionData = [
                'weather-data' => $weatherData
            ];
            Session::put($sessionData);
            return $weatherData;
        }
        return Session::get('weather-data');
    }
}