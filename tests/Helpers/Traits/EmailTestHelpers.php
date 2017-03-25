<?php

/**
 * Trait EmailTestHelpers
 * Helper methods for testing email in Laravel.
 */
trait EmailTestHelpers {
    protected $emails = [];

    /**
     * @return $this
     */
    protected function seeEmailWasSent()
    {
        $this->assertNotEmpty(
            $this->emails,
            "No email was sent"
        );
        return $this;
    }

    /**
     * @param $count
     * @return $this
     */
    protected function seeEmailsSent($count)
    {
        $emailsSent = count($this->emails);
        $this->assertCount(
            $count,
            $this->emails,
            "Expected {$count} emails to have been sent but {$emailsSent} email(s) sent."
        );
        return $this;
    }

    /**
     * @param $recipient
     * @param Swift_Message|null $message
     */
    protected function seeEmailTo($recipient, Swift_Message $message = null)
    {
        $this->assertArrayHasKey(
            $recipient,
            $this->getEmail($message)->getTo(),
            "No email was sent to {$recipient}."
        );
    }

    /**
     * @param $sender
     * @param Swift_Message|null $message
     */
    protected function seeEmailFrom($sender, Swift_Message $message = null)
    {
        $this->assertArrayHasKey(
            $sender,
            $this->getEmail($message)->getFrom(),
            "No email was sent from {$sender}."
        );
    }

    /**
     * @param $excerpt
     * @param Swift_Message|null $message
     */
    protected function seeEmailContains($excerpt, Swift_Message $message = null)
    {
        $this->assertContains(
            $excerpt,
            $this->getEmail($message)->getBody(),
            "The email body did not contain the input."
        );
    }

    /**
     * @param Swift_Message $email
     * @return $this
     */
    public function addEmail(Swift_Message $email)
    {
        $this->emails[] = $email;
        return $this;
    }

    /**
     * @param Swift_Message|null $message
     * @return mixed
     */
    protected function getEmail(Swift_Message $message = null)
    {
        $this->seeEmailWasSent();
        return $message ?: $this->lastEmail();
    }

    /**
     * @return mixed
     */
    protected function lastEmail()
    {
        return end($this->emails);
    }
}