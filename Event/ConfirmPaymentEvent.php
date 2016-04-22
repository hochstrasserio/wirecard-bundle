<?php

namespace Hochstrasser\WirecardBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ConfirmPaymentEvent extends Event
{
    /* Event name for event dispatcher */
    const NAME = 'hochstrasser_wirecard.confirm_payment';

    /* Event states */
    const RESPOND_WITH_SUCCESS = 1;
    const RESPOND_WITH_ERROR = 2;

    /* Payment states */
    const SUCCESS = 'SUCCESS';
    const PENDING = 'PENDING';
    const CANCEL = 'CANCEL';
    const FAILURE = 'FAILURE';

    /**
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $errorCode;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var string
     */
    private $state;

    function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Returns Wirecard response parameters sent to the confirmUrl
     *
     * @var array
     */
    function getData()
    {
        return $this->data;
    }

    /**
     * Returns the event's state
     *
     * @return string
     */
    function getState()
    {
        return $this->state;
    }

    /**
     * Returns the event's error code
     *
     * @return int
     */
    function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Returns the event's error message
     *
     * @return string
     */
    function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Checks if the supplied payment state matches the event's payment state
     *
     * @param string $state
     * @return bool
     */
    function isPaymentState($state)
    {
        return $this->data['paymentState'] === $state;
    }

    /**
     * Respond to the payment event with an error
     *
     * @param string $message
     * @param int $code
     */
    function respondWithError($message, $code)
    {
        $this->state = static::RESPOND_WITH_ERROR;
        $this->errorCode = $code;
        $this->errorMessage = $message;

        $this->stopPropagation();
    }

    /**
     * Respond to the payment event with a success message
     */
    function respondWithSuccess()
    {
        $this->state = static::RESPOND_WITH_SUCCESS;

        $this->stopPropagation();
    }
}
