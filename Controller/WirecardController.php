<?php

namespace Hochstrasser\WirecardBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Hochstrasser\Wirecard\Context;
use Hochstrasser\Wirecard\Fingerprint;
use Hochstrasser\WirecardBundle\Event;

class WirecardController
{
    private $context;
    private $eventDispatcher;

    function __construct(Context $context, EventDispatcherInterface $eventDispatcher)
    {
        $this->context = $context;
        $this->eventDispatcher = $eventDispatcher;
    }

    function confirmAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return new Response("", Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if (!$request->request->has('responseFingerprintOrder')) {
            return new Response("responseFingerprintOrder missing", Response::HTTP_BAD_REQUEST);
        }

        $fingerprint = Fingerprint::fromResponseParameters($request->request->all(), $this->context);
        $fingerprintIsValid = hash_equals((string) $fingerprint, $request->request->get('responseFingerprint'));

        if ($fingerprintIsValid === false) {
            return new Response("Fingerprint not valid", Response::HTTP_FORBIDDEN);
        }

        $event = new Event\ConfirmPaymentEvent($request->request->all());

        $this->eventDispatcher->dispatch(Event\ConfirmPaymentEvent::NAME, $event);

        if (!$event->isPropagationStopped() || $event->getState() === Event\ConfirmPaymentEvent::RESPOND_WITH_SUCCESS) {
            return new JsonResponse([
                'status' => 'OK',
                'errorCodes' => null,
                'QPAY-CONFIRMATION-RESPONSE' => [
                    'result' => 'OK',
                ],
            ]);
        }

        return new JsonResponse([
            'status' => 'NOK',
            'errorCodes' => $event->getErrorCode(),
            'QPAY-CONFIRMATION-RESPONSE' => [
                'result' => 'NOK',
                'message' => $event->getErrorMessage(),
            ],
        ]);
    }
}
