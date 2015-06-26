<?php namespace bookMe\Controller;

use bookMe\lib\Csrf\Csrf;
use bookMe\lib\Factories\WeatherModelFactory;
use bookMe\lib\DataAccess\RemoteCurlData;
use bookMe\lib\Validation\Validator;
use bookMe\Model\Property;
use bookMe\Model\PropertyImage;
use Exception;
use stdClass;

class PropertyController extends Controller {

    protected $_property;
    protected $_uploader;
    protected $_propertyImage;
    protected $_pageData;

    public function __construct()
    {
        parent::__construct();
        $this->_property = new Property();
        $this->_pageData = new stdClass();
        $this->_pageData->uri = $this->_request->getUri();
    }

    /**
     * Display the admin property list.
     *
     */
    public function index()
    {
        $this->_auth->redirectIfNotAuthenticated();
        $this->_pageData->csrfToken = Csrf::createToken();
        $this->_pageData->properties = $this->_property->getProperties();

        $this->_pageData->pageTitle = "Admin Property List";
        $this->_pageData->loggedIn = $this->_auth->checkLoggedIn();

        return $this->_view->make('admin/properties', $this->_pageData);
    }

    /**
     * Save a new property to the database.
     *
     */
    public function store()
    {
        Csrf::checkToken($this->_request->getInput('_CSRF'));
        $this->_pageData->csrfToken = Csrf::createToken();
        $validator = new Validator($this->_request);
        $rules = [
            'name' => [
                'required'
            ]
        ];
        $name = $this->_request->getInput('name');
        $rate = $this->_request->getInput('rate');
        $short_desc = $this->_request->getRawInput('short_desc');
        $long_desc = $this->_request->getRawInput('long_desc');
        $postData = $this->_request->getAllPostInput();
        if($validator->validate($rules, $postData))
        {
            $property = new Property($postData);
            if($property->save())
            {
                return header('Location: ' . POST_ADD_PROPERTY_URL);
            }
        }
        $this->_pageData->errors = $validator->getErrors();
        $this->_pageData->csrfToken = Csrf::createToken();
        $this->_pageData->pageTitle = "Create Property";
        $this->_pageData->loggedIn = $this->_auth->checkLoggedIn();
        $this->_pageData->name = $name;
        $this->_pageData->rate = $rate;
        $this->_pageData->short_desc = $short_desc;
        $this->_pageData->long_desc = $long_desc;

        return $this->_view->make('admin/create-property', $this->_pageData);

    }

    /**
     * Update a property.
     *
     * @param $id
     */
    public function update($id)
    {
        try {
            $this->_property = $this->_property->findOrFail($id);
        } catch(Exception $e) {
            return header('Location: ' . POST_UPDATE_PROPERTY_URL);
        }
        Csrf::checkToken($this->_request->getInput('_CSRF'));
        $this->_pageData->csrfToken = Csrf::createToken();
        $validator = new Validator($this->_request);
        $rules = [
            'name' => [
                'required'
            ]
        ];

        $name = $this->_request->getInput('name');
        $rate = $this->_request->getInput('rate');
        $short_desc = $this->_request->getRawInput('short_desc');
        $long_desc = $this->_request->getRawInput('long_desc');
        $postData = $this->_request->getAllPostInput();

        if($validator->validate($rules, $postData))
        {
            $this->_property->name = $name;
            $this->_property->rate = $rate;
            $this->_property->short_Desc = $short_desc;
            $this->_property->long_Desc = $long_desc;
            if($this->_property->save())
            {
                return header('Location: ' . POST_ADD_PROPERTY_URL);
            }
        }

        $this->_pageData->pageTitle = "Edit Property";
        $this->_pageData->loggedIn = $this->_auth->checkLoggedIn();
        $this->_pageData->uri = $this->_request->getUri();
        $this->_pageData->csrfToken = Csrf::createToken();
        $this->_pageData->property = $this->_property;
        $this->_pageData->errors = $validator->getErrors();
        return $this->_view->make('admin/edit-property', $this->_pageData);
    }

    /**
     * Show the view for creating a new property.
     *
     */
    public function create()
    {
        $this->_auth->redirectIfNotAuthenticated();

        $this->_pageData->pageTitle = "Create New Property";
        $this->_pageData->loggedIn = $this->_auth->checkLoggedIn();
        $this->_pageData->uri = $this->_request->getUri();
        $this->_pageData->csrfToken = Csrf::createToken();

        return $this->_view->make('admin/create-property', $this->_pageData);
    }

    /**
     * Show the view for editing a property.
     *
     * @param $id
     */
    public function edit($id)
    {
        $this->_auth->redirectIfNotAuthenticated();
        try {
            $this->_property = $this->_property->getProperty($id);
        } catch(Exception $e) {
            return header('Location: ' . POST_ADD_PROPERTY_URL);
        }
        if(is_null($this->_property))
        {
            return header('Location: ' . POST_ADD_PROPERTY_URL);
        }
        $this->_pageData->pageTitle = "Edit Property";
        $this->_pageData->loggedIn = $this->_auth->checkLoggedIn();
        $this->_pageData->uri = $this->_request->getUri();
        $this->_pageData->csrfToken = Csrf::createToken();
        $this->_pageData->property = $this->_property;
        return $this->_view->make('admin/edit-property', $this->_pageData);
    }

    /**
     * Display the property details page.
     *
     * @param $id
     */
    public function show($id)
    {
        try {
            $this->_property = $this->_property->getProperty($id);
            if(is_null($this->_property))
            {
                return header("Location: " . SITE_URL);
            }
        } catch(Exception $e) {
            return header("Location: " . SITE_URL);
        }

        try{
            $weatherModel = WeatherModelFactory::make(new RemoteCurlData());
            $this->_pageData->temperature = $weatherModel->getTemp();
        } catch(Exception $e) {
            $this->_pageData->temperature = '-';
        }

        $this->_pageData->property = $this->_property;
        $this->_pageData->pageTitle = $this->_property->name . " - Details";

        return $this->_view->make('public/property-detail', $this->_pageData);
    }

    /**
     * Delete a property.
     *
     * @param $id
     */
    public function destroy($id)
    {
        Csrf::checkToken($this->_request->getInput('_CSRF'));
        try {
            $this->_propertyImage = new PropertyImage();
            $this->_property = $this->_property->findOrFail($id);
            $this->_propertyImage = $this->_propertyImage->where('pid', '=', $id)->get();

            foreach($this->_propertyImage as $image)
            {
                //Delete related image models and image files.
                unlink($_SERVER['DOCUMENT_ROOT'] . $image->image_full_path);
                $image->delete();
            }

            $this->_property->delete();
        } catch(Exception $e) {
            return header('Location: ' . POST_DELETE_PROPERTY_URL);
        }
        return header('Location: ' . POST_DELETE_PROPERTY_URL);
    }
}