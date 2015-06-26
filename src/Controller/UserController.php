<?php namespace bookMe\Controller;

use bookMe\lib\Csrf\Csrf;
use bookMe\lib\Http\Response;
use bookMe\Model\User;
use bookMe\lib\Validation\Validator;
use Exception;
use stdClass;

class UserController extends Controller {

    protected $_authenticatedUser;
    protected $_user;
    protected $_pageData;
    protected $_response;

    public function __construct()
    {
        parent::__construct();
        $this->_user = new User();
        $this->_auth->redirectIfNotAuthenticated();
        $this->_authenticatedUser = $this->_auth->getAuthenticatedUser();
        $this->_response = new Response();
        $this->_pageData = new stdClass();
        $this->_pageData->uri = $this->_request->getUri();
    }

    /**
     * View the users.
     *
     */
    public function index()
    {
        $this->_pageData->csrfToken = Csrf::createToken();
        $this->_pageData->pageTitle = "View Users";
        $this->_pageData->loggedIn = $this->_auth->checkLoggedIn();
        $this->_pageData->currentUser = $this->_authenticatedUser;
        $this->_pageData->users = $this->_user->all();

        return $this->_view->make('admin/users', $this->_pageData);
    }

    /**
     * Save a new user to the database.
     *
     */
    public function store()
    {
        Csrf::checkToken($this->_request->getInput('_CSRF'));

        $validator = new Validator($this->_request);
        $rules = [
            'name' => [
                'required'
            ],
            'email' => [
                'required'
            ],
            'password' => [
                'required',
                'password'
            ],
            'password-repeat' => [
                'required'
            ]
        ];

        $name = $this->_request->getInput('name');
        $email = $this->_request->getInput('email');
        $password = $this->_request->getRawInput('password');
        $postData = $this->_request->getAllPostInput();
        if($validator->validate($rules, $postData))
        {
            $this->_user->name = $name;
            $this->_user->email = $email;
            $this->_user->password = $this->_auth->hashPassword($password);
            if($this->_user->save())
            {
                return header('Location: ' . POST_REGISTER_URL);
            }
            //The email address is already registered.
            $this->_pageData->errors = ['email' => ['That email address is already registered.']];
        }else {
            $this->_pageData->errors = $validator->getErrors();
        }


        $this->_pageData->csrfToken = Csrf::createToken();
        $this->_pageData->pageTitle = "Create User";
        $this->_pageData->loggedIn = $this->_auth->checkLoggedIn();
        $this->_pageData->name = $name;
        $this->_pageData->email = $email;

        return $this->_view->make('admin/create-user', $this->_pageData);
    }

    /**
     * Show view for creating new user.
     *
     */
    public function create()
    {
        unset($_SESSION['errors']);

        $this->_pageData->csrfToken = Csrf::createToken();
        $this->_pageData->pageTitle = 'Create User';
        $this->_pageData->loggedIn = $this->_auth->checkLoggedIn();

        return $this->_view->make('admin/create-user', $this->_pageData);
    }

    /**
     * Display the view for updating a user.
     *
     */
    public function edit()
    {
        $this->_pageData->id = $this->_authenticatedUser;
        $this->_pageData->csrfToken = Csrf::createToken();
        $this->_pageData->pageTitle = 'Create User';
        $this->_pageData->loggedIn = $this->_auth->checkLoggedIn();

        return $this->_view->make('admin/change-password', $this->_pageData);
    }

    /**
     * Update a user.
     *
     * @param $id
     * @return string|void
     */
    public function update($id)
    {
        Csrf::checkToken($this->_request->getInput('_CSRF'));
        if($this->_request->isAjax())
        {
            $validator = new Validator($this->_request);
            $rules = [
                'password' => [
                    'required',
                    'password'
                ],
                'password-repeat' => [
                    'required'
                ]
            ];

            try {
                $this->_user = $this->_user->findOrFail($id);
            } catch(Exception $e) {
                return header('Location: ' . POST_REGISTER_URL);
            }

            $password = $this->_request->getInput('password');
            $postData = $this->_request->getAllPostInput();
            $this->_user->password = $this->_auth->hashPassword($password);
            if($validator->validate($rules, $postData))
            {
                if(!$this->_user->save())
                {
                    $response = [
                        'status' => 'error'
                    ];
                    return $this->_response->returnJson($response);
                }
            }
            $response = [
                'status' => 'success'
            ];
            return $this->_response->returnJson($response);
        }
        return header('Location: ' . POST_REGISTER_URL);
    }

    /**
     * Delete a user.
     *
     * @param $id
     */
    public function delete($id)
    {
        Csrf::checkToken($this->_request->getInput('_CSRF'));
        try {
            $this->_user = $this->_user->findOrFail($id);
        } catch(Exception $e) {
            return header('Location: ' . POST_DELETE_USER_URL);
        }
        $isAdmin = $this->_auth->isAdmin();

        //Prevent admin account delete.
        if($id === 1)
        {
            return header('Location: ' . POST_DELETE_USER_URL);
        }

        //An admin can delete any user but themself.
        if($isAdmin)
        {
            $this->_user->delete();
        }

        //A user can only delete themself.
        if(intval($this->_authenticatedUser) === $id)
        {
            $this->_user->delete();
        }

        return header('Location: ' . POST_DELETE_USER_URL);
    }
}