<?php

namespace Hochstrasser\WirecardBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ConfirmPaymentEvent extends Event
{
    const NAME = 'hochstrasser_wirecard.confirm_payment';

    const RESPOND_WITH_SUCCESS = 1;
    const RESPOND_WITH_ERROR = 2;

    const SUCCESS = 'SUCCESS';
    const PENDING = 'PENDING';
    const CANCEL = 'CANCEL';
    const FAILURE = 'FAILURE';

    private $data;
    private $errorCode;
    private $errorMessage;
    private $state;

    function __construct($data)
    {
        $this->data = $data;
    }

    function getData()
    {
        return $this->data;
    }

    function getState()
    {
        return $this->state;
    }

    function getErrorCode()
    {
        return $this->errorCode;
    }

    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    function isPaymentState($state)
    {
        return $this->data['paymentState'] === $state;
    }

    function respondWithError($message, $code)
    {
        $this->state = static::RESPOND_WITH_ERROR;
        $this->errorCode = $code;
        $this->errorMessage = $message;

        $this->stopPropagation();
    }

    function respondWithSuccess()
    {
        $this->state = static::RESPOND_WITH_SUCCESS;

        $this->stopPropagation();
    }
}
