<?php

namespace Hochstrasser\WirecardBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

use Hochstrasser\Wirecard\Fingerprint;
use Hochstrasser\WirecardBundle\Event\ConfirmPaymentEvent;

class WirecardControllerTest extends WebTestCase
{
    /**
     * @test
     */
    function allowsOnlyPostMethod()
    {
        $client = new Client(static::createKernel());
        $client->request('GET', '/wirecard/confirm');

        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    function respondsWithBadRequestWhenFingerprintOrderIsMissing()
    {
        $client = new Client(static::createKernel());
        $client->request('POST', '/wirecard/confirm', []);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    function respondsWithBadRequestWhenFingerprintIsWrong()
    {
        $client = new Client(static::createKernel());
        $client->request('POST', '/wirecard/confirm', ['responseFingerprint' => 'abcd', 'responseFingerprintOrder' => 'foo']);

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * @test
     */
    function dispatchesConfirmPaymentEvent()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $client = new Client($kernel);

        $listener = function (ConfirmPaymentEvent $event) {
            $this->assertTrue($event->isPaymentState(ConfirmPaymentEvent::SUCCESS));
            $event->respondWithSuccess();
        };

        $kernel->getContainer()
            ->get('event_dispatcher')
            ->addListener(ConfirmPaymentEvent::NAME, $listener);

        $fingerprintOrder = ['paymentState'];

        $responseParameters = [
            'paymentState' => ConfirmPaymentEvent::SUCCESS,
            'responseFingerprintOrder' => join(',', $fingerprintOrder),
        ];

        $responseParameters['responseFingerprint'] = (string) Fingerprint::fromParameters($responseParameters)
            ->setFingerprintOrder($fingerprintOrder)
            ->setContext($kernel->getContainer()->get('hochstrasser_wirecard.context'));

        $client->request('POST', '/wirecard/confirm', $responseParameters);

        $this->assertEquals(
            '{"status":"OK","errorCodes":null,"QPAY-CONFIRMATION-RESPONSE":{"result":"OK"}}',
            $client->getResponse()->getContent()
        );
    }

    /**
     * @test
     */
    function dispatchesConfirmPaymentEventAndRespondWithError()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $client = new Client($kernel);

        $listener = function (ConfirmPaymentEvent $event) {
            $this->assertTrue($event->isPaymentState(ConfirmPaymentEvent::FAILURE));
            $event->respondWithError('Failure', 1234);
        };

        $kernel->getContainer()
            ->get('event_dispatcher')
            ->addListener(ConfirmPaymentEvent::NAME, $listener);

        $fingerprintOrder = ['paymentState'];

        $responseParameters = [
            'paymentState' => ConfirmPaymentEvent::FAILURE,
            'responseFingerprintOrder' => join(',', $fingerprintOrder),
        ];

        $responseParameters['responseFingerprint'] = (string) Fingerprint::fromParameters($responseParameters)
            ->setFingerprintOrder($fingerprintOrder)
            ->setContext($kernel->getContainer()->get('hochstrasser_wirecard.context'));

        $client->request('POST', '/wirecard/confirm', $responseParameters);

        $this->assertEquals(
            '{"status":"NOK","errorCodes":1234,"QPAY-CONFIRMATION-RESPONSE":{"result":"NOK","message":"Failure"}}',
            $client->getResponse()->getContent()
        );
    }
}
