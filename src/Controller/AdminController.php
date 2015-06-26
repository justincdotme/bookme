<?php namespace bookMe\Controller;

use bookMe\lib\Csrf\Csrf;
use stdClass;

class AdminController extends Controller {

    protected $_pageData;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display the login page.
     *
     */
    public function index()
    {
        $this->_auth->redirectIfNotAuthenticated();
        header("Location: /admin/reservations");
    }

    /**
     * Log a user into the application.
     *
     */
    public function login()
    {
        $this->_pageData = new stdClass();
        $this->_pageData->uri = $this->_request->getUri();
        $this->_pageData->pageTitle = "Login";
        $this->_pageData->loggedIn = $this->_auth->checkLoggedIn();

        if($this->_request->getMethod() === 'post')
        {
            $credentials['email'] = $this->_request->getInput('email');
            $credentials['password'] = $this->_request->getInput('password');
            $this->_password = $this->_request->getRawInput('password');
            Csrf::checkToken($this->_request->getInput('_CSRF'));
            if(!$loggedIn = $this->_auth->login($credentials))
            {
               $this->_pageData->loginError = INVALID_USER_ERROR;
            }
        }
        return $this->_view->make('admin/login', $this->_pageData);
    }

    /**
     * Log a user out of the application.
     *
     * @return bool
     */
    public function logout()
    {
        return $this->_auth->logout();
    }
}