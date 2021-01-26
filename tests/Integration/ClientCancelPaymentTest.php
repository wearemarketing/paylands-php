<?php

namespace WAM\Paylands\Tests\Integration;

use WAM\Paylands\ClientInterface;
use WAM\Paylands\Tests\ClientBaseTestCase;

class ClientCancelPaymentTest extends ClientBaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->client->setOperative(ClientInterface::OPERATIVE_DEFERRED);
    }

    public function testCancelPayment()
    {
        $payment = $this->client->createPayment(
            $this->customerExternalId,
            100,
            'Test payment order',
            $this->apiPaymentServiceId
        );

        $card = $this->client->saveCard(
            $this->customerExternalId,
            'John Doe',
            '4548812049400004',
            '30',
            '12',
            '987'
        );

        $orderUuid = $payment['order']['uuid'];
        $cardUuid = $card['Source']['uuid'];

        $deferredPayment = $this->client->directPayment('123.123.123.123', $orderUuid, $cardUuid);

        $this->assertSame('OK', $deferredPayment['message']);
        $this->assertSame(200, $deferredPayment['code']);
        $this->assertCount(1, $deferredPayment['order']['transactions']);
        $this->assertSame('SUCCESS', $deferredPayment['order']['transactions'][0]['status']);
        $this->assertSame('DEFERRED', $deferredPayment['order']['transactions'][0]['operative']);

        $cancellation = $this->client->cancelPayment($orderUuid);

        $this->assertSame('OK', $cancellation['message']);
        $this->assertSame(200, $cancellation['code']);
        $this->assertFalse($cancellation['order']['paid']);
        $this->assertCount(2, $cancellation['order']['transactions']);
        // fixme: next line is commented because status may be refused due to sandbox restrictions
        // $this->assertSame('SUCCESS', $cancellation['order']['transactions'][1]['status']);
        $this->assertSame('CANCELLATION', $cancellation['order']['transactions'][1]['operative']);
    }
}
