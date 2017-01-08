<?php namespace bookMe\lib\Validation;

/**
 * Class Validator
 *
 * This class contains the methods used to validate form input.
 *
 * The Validator class contains 2 public methods: validate and getErrors.
 * The validate method accepts 2 parameters: $rules (array) and $postData (array).
 * Run the validate method first to populate the errors array, then run getErrors to return any errors.
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

class Validator implements ValidatorInterface {

    protected $_errors;
    protected $_hasErrors;

    public function __construct()
    {
        $this->_hasErrors = false;
        $this->_errors = [];
    }

    /**
     * Check that required fields have a value.
     *
     * @param $field
     * @param $value
     * @return bool
     */
    protected function checkRequired($field, $value)
    {
        if(empty(trim($value)))
        {
            $this->_hasErrors = true;

            if(!array_key_exists($field, $this->_errors))
            {
                $this->_errors[$field] = [];
                array_push($this->_errors[$field], "The $field field is required");
            }else {
                array_push($this->_errors[$field], "The $field field is required");
            }
            return false;
        }
        return true;
    }

    /**
     * Ensure that password fields match.
     *
     * @return bool
     */
    protected function matchPasswords($pass1, $pass2)
    {
        if($pass1 !== $pass2)
        {
            $this->_hasErrors = true;

            if(!array_key_exists('password', $this->_errors))
            {
                $this->_errors['password'] = [];
                array_push($this->_errors['password'], 'The passwords do not match');
            }else {
                array_push($this->_errors['password'], 'The passwords do not match');
            }
            return false;
        }
        return true;
    }

    /**
     * Determine if an email address was in the correct format.
     *
     * @param $field
     * @return bool
     */
    protected function validateEmailAddress($field)
    {
        if(!filter_var($field, FILTER_VALIDATE_EMAIL))
        {
            $this->_hasErrors = true;

            if(!array_key_exists('email_address', $this->_errors))
            {
                $this->_errors['email_address'] = [];
                array_push($this->_errors['email_address'], 'The email address is not valid');
            }else {
                array_push($this->_errors['email_address'], 'The email address is not valid');
            }
            return false;
        }
        return true;
    }

    /**
     * Determine if a phone number is in valid US format.
     *
     * @param $field
     * @return bool
     */
    protected function validateUsPhone($field)
    {
        $valid = preg_match("/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i", $field);
        if(!$valid)
        {
            $this->_hasErrors = true;

            if(!array_key_exists('home_phone', $this->_errors))
            {
                $this->_errors['home_phone'] = [];
                array_push($this->_errors['home_phone'], 'The phone number is not valid');
            }else {
                array_push($this->_errors['home_phone'], 'The phone number is not valid');
            }
            return false;
        }
        return true;
    }

    /**
     * Trigger validation methods according to rules.
     *
     * @param array $rules
     * @param array $postData
     * @return bool
     */
    public function validate(array $rules, array $postData)
    {
        foreach($rules as $fieldName => $rule)
        {
            foreach($rule as $requirement)
            {
                switch($requirement)
                {
                    case 'required':
                        $this->checkRequired($fieldName, $postData[$fieldName]);
                        break;
                    case 'password':
                        $this->matchPasswords($postData['password'], $postData['password-repeat']);
                        break;
                    case 'email':
                        $this->validateEmailAddress($postData[$fieldName]);
                        break;
                    case 'phone':
                        $this->validateUsPhone($postData[$fieldName]);
                        break;
                }
            }
        }
        return $this->_hasErrors ? false : true;
    }


    /**
     * Return the error list array.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}