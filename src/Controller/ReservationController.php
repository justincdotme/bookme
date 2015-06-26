<?php namespace bookMe\Controller;

use bookMe\lib\Csrf\Csrf;
use bookMe\lib\Email\Email;
use bookMe\lib\Reservation\ReservationAvailability;
use bookMe\lib\Http\Response;
use bookMe\lib\Validation\Validator;
use bookMe\Model\Customer;
use bookMe\Model\Reservation;
use Exception;
use stdClass;

class ReservationController extends Controller {

    protected $_reservation;
    protected $_checkIn;
    protected $_checkOut;
    protected $_propertyId;
    protected $_response;
    protected $_pageData;

    public function __construct()
    {
        parent::__construct();
        $this->_response = new Response();
        $this->_reservation = new Reservation;
    }

    /**
     * View the reservations.
     *
     */
    public function index()
    {
        $this->_auth->redirectIfNotAuthenticated();
        $this->_pageData = new stdClass();
        $this->_pageData->uri = $this->_request->getUri();
        $this->_pageData->loggedIn = $this->_auth->checkLoggedIn();
        $this->_pageData->pageTitle = "View Reservations";
        $this->_pageData->csrfToken = Csrf::createToken();
        $this->_pageData->reservations = $this->_reservation->getAllReservations();
        return $this->_view->make('admin/reservations', $this->_pageData);
    }

    /**
     * Save a new reservation to the database.
     *
     * @return string
     */
    public function store()
    {
        //This is not a sensitive action so I am not going to run a CSRF check here (re: performance, unneeded)
        //Validate the input
        $validator = new Validator($this->_request);
        $rules = [
            'first_name' => [
                'required'
            ],
            'last_name' => [
                'required'
            ],
            'addr_street_1' => [
                'required'
            ],
            'addr_city' => [
                'required'
            ],
            'addr_state' => [
                'required'
            ],
            'addr_zip' => [
                'required'
            ],
            'email_address' => [
                'required',
                'email'
            ],
            'home_phone' => [
                'required',
                'phone'
            ],
            'pid' => [
                'required'
            ],
            'guests' => [
                'required'
            ],
            'check_in' => [
                'required'
            ],
            'check_out' => [
                'required'
            ]
        ];
        $postData = $this->_request->getAllPostInput();

        if(!$validator->validate($rules, $postData))
        {
            $response = [
                'status' => 'error',
                'errors' => $validator->getErrors()
            ];
            return $this->_response->returnJson($response);
        }

        //Ensure check out date is at least 1 day after check in date.
        $checkInDate = date('Y-m-d', strtotime($this->_request->getInput("check_in")));
        $checkOutDate = date('Y-m-d', strtotime($this->_request->getInput("check_out")));
        if(date_diff(date_create($checkInDate), date_create($checkOutDate))->days < 1)
        {
            $errors = new stdClass();
            $errors->check_in = ['Please select a different date range'];
            $errors->check_out = ['Please select a different date range'];
            $response = [
                'status' => 'error',
                'errors' => $errors
            ];
            return $this->_response->returnJson($response);
        }

        //Create the customer object
        $customer = new Customer();
        $customer->first_name = $this->_request->getInput("first_name");
        $customer->last_name = $this->_request->getInput("last_name");
        $customer->addr_street_1 = $this->_request->getInput("addr_street_1");
        $customer->addr_street_2 = $this->_request->getInput("addr_street_2");
        $customer->addr_city = $this->_request->getInput("addr_city");
        $customer->addr_state = $this->_request->getInput("addr_state");
        $customer->addr_zip = $this->_request->getInput("addr_zip");
        $customer->email_address = $this->_request->getInput("email_address");
        $customer->home_phone = str_replace('-', '', $this->_request->getInput("home_phone"));


        //Create the reservation object
        $this->_reservation->pid = $this->_request->getInput("pid");
        $this->_reservation->guests = $this->_request->getInput("guests");
        $this->_reservation->status = 0;
        $this->_reservation->check_in = $checkInDate;
        $this->_reservation->check_out = $checkOutDate;

        //Verify that the property is available.
        $availability = new ReservationAvailability($this->_reservation);
        if(!$availability->check())
        {
            $errors = new stdClass();
            $errors->check_in = ['Please select a different date rangel'];
            $errors->check_out = ['Please select a different date rangel'];
            $response = [
                'status' => 'error',
                'errors' => $errors
            ];
            return $this->_response->returnJson($response);
        }

        //Create the reservation
        if(!$this->book($customer, $this->_reservation))
        {
            $response = [
                'status' => 'error'
            ];
            return $this->_response->returnJson($response);
        }

        //Send email
        /*$contents = new stdClass();
        $contents->sender = EMAIL_FROM;
        $contents->recipient = $customer->email_address;
        $contents->recipientName = $customer->first_name . ' ' . $customer->last_name;
        $contents->subject = RENTAL_CONFIRMATION_SUBJECT;
        $contents->body = 'Thank you for reserving this property, ' . $customer ->first_name . ' ' . $customer->last_name . '.' . PHP_EOL;
        $email = new Email();
        try {
            $email->send($contents);
        } catch(Exception $e) {
            //Silent error, sending the email is not critical.
        }*/

        //Return success message.
        $response = [
            'status' => 'success'
        ];
        return $this->_response->returnJson($response);
    }

    /**
     * Check availability of property.
     *
     * @return string
     */
    public function check()
    {
        //Validate the input
        $validator = new Validator($this->_request);
        $rules = [
            'check_in' => [
                'required'
            ],
            'check_out' => [
                'required'
            ]
        ];
        $postData = $this->_request->getAllPostInput();
        if(!$validator->validate($rules, $postData))
        {
            $response = [
                'status' => 'error',
                'errors' => $validator->getErrors()
            ];
            return $this->_response->returnJson($response);
        }
        $this->_reservation->pid = $postData['property'];
        $this->_reservation->check_in = date('Y-m-d', strtotime($this->_request->getInput("check_in")));
        $this->_reservation->check_out = date('Y-m-d', strtotime($this->_request->getInput("check_out")));

        $availability = new ReservationAvailability($this->_reservation);
        if(!$availability->check())
        {
            $response = [
                'status' => 'unavailable'
            ];
            return $this->_response->returnJson($response);
        }
        $response = [
            'status' => 'success'
        ];
        return $this->_response->returnJson($response);
    }

    /**
     * Update a reservation.
     *
     * @param $id
     * @return string
     */
    public function update($id)
    {
        $this->_auth->redirectIfNotAuthenticated();
        if(!Csrf::checkToken($this->_request->getInput('_CSRF')))
        {
            $response = [
                'status' => 'error'
            ];
            return $this->_response->returnJson($response);
        }

        try {
            $this->_reservation = $this->_reservation->findOrFail($id);
            $this->_reservation->status = $this->_request->getInput('reservation-status');
            $this->_reservation->save();
        } catch(Exception $e) {
            $response = [
                'status' => 'error',
                'message' => $e
            ];
            return $this->_response->returnJson($response);
        }

        $response = [
            'status' => 'success'
        ];
        return $this->_response->returnJson($response);
    }

    /**
     * Book a reservation.
     *
     * @return bool
     * @throws \Exception
     */
    protected function book($customer, $reservation)
    {
        if(!$customer->save())
        {
            return false;
        }
        $reservation->cid = $customer->cid;
        if(!$reservation->save())
        {
            $customer->delete();
            return false;
        }
        return true;
    }
}