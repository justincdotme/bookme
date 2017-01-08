<?php namespace bookMe\lib\Email;

use stdClass;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/**
 * Class Email
 *
 * This class is responsible sending email.
 *
 * This class has 1 public method, send which accepts 1 parameter $message (stdClass).
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

class Email implements EmailInterface {

    protected $_transport;
    protected $_message;
    protected $_mailer;

    public function __construct()
    {
        $this->_transport = Swift_SmtpTransport::newInstance("smtp.mailserver.com", 25)
        ->setUsername('username')
        ->setPassword('password');
        $this->_message = Swift_Message::newInstance();
        $this->_mailer = Swift_Mailer::newInstance($this->_transport);
    }

    /**
     * Send an email.
     *
     * @param stdClass $email
     * @return bool
     */
    public function send(stdClass $email)
    {
        $this->_message->setTo(array(
            $email->recipient => $email->recipientName
        ));
        $this->_message->setSubject($email->subject);
        $this->_message->setBody($email->body);
        $this->_message->setFrom($email->sender);

        $this->_mailer->send($this->_message, $failedRecipients);
    }
}
