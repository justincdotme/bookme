<?php

class TestingMailEventListener implements \Swift_Events_EventListener
{
    protected $testClass;

    /**
     * TestingMailEventListener constructor.
     * @param $testClass
     */
    function __construct($testClass)
    {
        $this->testClass = $testClass;
    }

    /**
     * @param $event
     */
    public function beforeSendPerformed($event)
    {
        $this->testClass->addEmail($event->getMessage());
    }
}