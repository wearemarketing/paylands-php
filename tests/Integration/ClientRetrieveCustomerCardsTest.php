<?php

namespace WAM\Paylands\Tests\Integration;

use WAM\Paylands\Tests\ClientBaseTestCase;

class ClientRetrieveCustomerCardsTest extends ClientBaseTestCase
{
    public function testRetrieveCustomerCardsWhenDefaultOptions()
    {
        $this->client->saveCard(
            $this->customerExternalId,
            'Jonh Doe',
            '4548812049400004',
            '20',
            '12',
            '987'
        );

        $customerCards = $this->client->retrieveCustomerCards($this->customerExternalId);

        $this->assertSame('OK', $customerCards['message']);
        $this->assertSame(200, $customerCards['code']);
        $this->assertCount(1, $customerCards['cards']);
        $card = $customerCards['cards'][0];
        $this->assertEquals('CARD', $card['object']);
        $this->assertEquals('454881', $card['bin']);
        $this->assertEquals('0004', $card['last4']);
        $this->assertNotEmpty($card['uuid']);
    }

    public function testRetrieveCustomerCardsAllNotUnique()
    {
        $this->markTestSkipped('Need to verify with Paylands provider first');

        $card = $this->client->saveCard(
            $this->customerExternalId,
            'Jonh Doe',
            '4111111111111111',
            '20',
            '12',
            '987'
        );

        $payment = $this->client->createPayment($this->customerExternalId, 100, 'description', $this->apiPaymentServiceId);
        $details = $this->client->directPayment('123.123.123.123', $payment['order']['uuid'], $card['Source']['uuid']);

        $customerCards = $this->client->retrieveCustomerCards($this->customerExternalId, 'ALL', 'false');

        $this->assertSame('OK', $customerCards['message']);
        $this->assertSame(200, $customerCards['code']);
        $this->assertCount(1, $customerCards['cards']);
        $card = $customerCards['cards'][0];
        $this->assertEquals('CARD', $card['object']);
        $this->assertEquals('411111', $card['bin']);
        $this->assertEquals('1111', $card['last4']);
        $this->assertNotEmpty($card['uuid']);
    }
}
