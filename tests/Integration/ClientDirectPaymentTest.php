<?php

namespace WAM\Paylands\Tests\Integration;

use WAM\Paylands\Tests\ClientBaseTestCase;

class ClientDirectPaymentTest extends ClientBaseTestCase
{
    public function testDirectPaymentSuccess()
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
            '30',
            '12',
            '987'
        );

        $orderUuid = $payment['order']['uuid'];
        $cardUuid = $card['Source']['uuid'];

        $directPayment = $this->client->directPayment('123.123.123.123', $orderUuid, $cardUuid);

        $this->assertSame('OK', $directPayment['message']);
        $this->assertSame(200, $directPayment['code']);
        $this->assertTrue($directPayment['order']['paid']);
        $this->assertCount(1, $directPayment['order']['transactions']);
        $this->assertSame('SUCCESS', $directPayment['order']['transactions'][0]['status']);
    }

    public function testDirectPaymentFails()
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
            '4111111111111111',
            '30',
            '12',
            '987'
        );

        $orderUuid = $payment['order']['uuid'];
        $cardUuid = $card['Source']['uuid'];

        $directPayment = $this->client->directPayment('123.123.123.123', $orderUuid, $cardUuid);

        $this->assertSame('OK', $directPayment['message']);
        $this->assertSame(200, $directPayment['code']);
        $this->assertFalse($directPayment['order']['paid']);
        $this->assertCount(1, $directPayment['order']['transactions']);
        $this->assertSame('REFUSED', $directPayment['order']['transactions'][0]['status']);
    }
}
