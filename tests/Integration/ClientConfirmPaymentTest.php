<?php

namespace WAM\Paylands\Tests\Integration;

use WAM\Paylands\ClientInterface;
use WAM\Paylands\Tests\ClientBaseTestCase;

class ClientConfirmPaymentTest extends ClientBaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->client->setOperative(ClientInterface::OPERATIVE_DEFERRED);
    }

    public function testConfirmPayment()
    {
        $payment = $this->client->createPayment(
            $this->customerExternalId,
            100,
            'Test payment order',
            $this->apiPaymentServiceId
        );

        $card = $this->client->saveCard(
            $this->customerExternalId,
            'Jonh Doe',
            '4548812049400004',
            '20',
            '12',
            '987'
        );

        $orderUuid = $payment['order']['uuid'];
        $cardUuid = $card['Source']['uuid'];

        $deferredPayment = $this->client->directPayment('123.123.123.123', $orderUuid, $cardUuid);

        $this->assertSame('OK', $deferredPayment['message']);
        $this->assertSame(200, $deferredPayment['code']);
        $this->assertTrue($deferredPayment['order']['paid']);
        $this->assertCount(1, $deferredPayment['order']['transactions']);
        $this->assertSame('SUCCESS', $deferredPayment['order']['transactions'][0]['status']);
        $this->assertSame('DEFERRED', $deferredPayment['order']['transactions'][0]['operative']);

        $confirmation = $this->client->confirmPayment($orderUuid);

        $this->assertSame('OK', $confirmation['message']);
        $this->assertSame(200, $confirmation['code']);
        $this->assertTrue($confirmation['order']['paid']);
        $this->assertCount(2, $confirmation['order']['transactions']);
        $this->assertSame('SUCCESS', $confirmation['order']['transactions'][1]['status']);
        $this->assertSame('CONFIRMATION', $confirmation['order']['transactions'][1]['operative']);
    }
}
