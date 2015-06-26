<?php namespace bookMe\Controller;

use bookMe\lib\Factories\WeatherModelFactory;
use bookMe\lib\DataAccess\RemoteCurlData;
use bookMe\Model\Property;
use Exception;
use stdClass;

class HomeController extends Controller {

    protected $_property;
    protected $_pageData;

    public function __construct()
    {
        parent::__construct();
        $this->_property = new Property();
        $this->_pageData = new stdClass();
        $this->_pageData->uri = $this->_request->getUri();
    }

    /**
     * Return the homepage view.
     *
     */
    public function index()
    {
        $this->_pageData->pageTitle = "Home";
        try{
            $weatherModel = WeatherModelFactory::make(new RemoteCurlData());
            $this->_pageData->temperature = $weatherModel->getTemp();
        } catch(Exception $e) {
            $this->_pageData->temperature = '-';
        }

        $this->_pageData->properties = $this->_property->getProperties();

        return $this->_view->make('public/home', $this->_pageData);
    }
}