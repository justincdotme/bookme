<?php namespace bookMe\lib\Http;

/**
 * Class Request
 *
 * This class contains helper methods for working with HTTP requests.
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

class Request implements RequestInterface {

    protected $_method;

    public function __construct()
    {
        //Allow for HTTP request method faking via hidden input field.
        $postMethod = filter_input(INPUT_POST, '_METHOD', FILTER_SANITIZE_STRING);
        $this->_method = is_null($postMethod) ? strtolower($_SERVER['REQUEST_METHOD']) : strtolower($postMethod);
    }

    /**
     * Determine if request was sent via AJAX.
     *
     * @return bool
     */
    public function isAjax()
    {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        {
            return true;
        }
        return false;
    }

    /**
     * Return the request method.
     *
     * @return mixed|string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Return the request URI (minus leading and tailing /).
     *
     * @return mixed
     */
    public function getUri()
    {
        return filter_var(trim(strtolower($_SERVER['REQUEST_URI']), '/'), FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize $_POST data.
     *
     * @param $field
     * @return mixed
     */
    public function getInput($field)
    {
        return isset($_POST[$field]) ? filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING) : null;
    }

    /**
     * Return raw $_POST data.
     * Used for files, passwords, etc.
     *
     * @param $field
     * @return mixed
     */
    public function getRawInput($field)
    {
        return isset($_POST[$field]) ? $_POST[$field] : null;
    }

    /**
     * Return the uploaded file information.
     *
     * @param $file
     * @return bool
     */
    public function getUploadedFile($file)
    {
        return isset($_FILES[$file]) ? $_FILES[$file] : false;
    }

    /**
     * Return raw POST data array.
     *
     * @return null
     */
    public function getRawPostInput()
    {
        return isset($_POST) ? $_POST : null;
    }

    /**
     * Get filtered POST data array.
     *
     * @return array|bool
     */
    public function getAllPostInput()
    {
        if(isset($_POST))
        {
            $postData = [];
            foreach($_POST as $field => $value)
            {
                $postData[$field] = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
            }
            return $postData;
        }
        return false;
    }
}