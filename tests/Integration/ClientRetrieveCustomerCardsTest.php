<?php

namespace WAM\Paylands\Tests\Integration;

use WAM\Paylands\Tests\ClientBaseTestCase;

class ClientRetrieveCustomerCardsTest extends ClientBaseTestCase
{
    public function testRetrieveCustomerCardsWithDefaultOptionsValidatedCardsAndNotUnique()
    {
        $this->client->saveCard(
            $this->customerExternalId,
            'Jonh Doe',
            '4548812049400004',
            '20',
            '12',
            '987',
            true,
            $this->apiPaymentServiceId
        );

        $this->client->saveCard(
            $this->customerExternalId,
            'Antonio Garcia',
            '4548812049400004',
            '20',
            '12',
            '987',
            true,
            $this->apiPaymentServiceId
        );

        $this->client->saveCard(
            $this->customerExternalId,
            'Kevin Spacy',
            '4111111111111111',
            '20',
            '12',
            '987'
        );

        $customerCards = $this->client->retrieveCustomerCards($this->customerExternalId);

        $this->assertSame('OK', $customerCards['message']);
        $this->assertSame(200, $customerCards['code']);
        $this->assertCount(2, $customerCards['cards']);
        $card = $customerCards['cards'][0];
        $this->assertEquals('Jonh Doe', $card['holder']);
        $this->assertEquals('CARD', $card['object']);
        $this->assertEquals('454881', $card['bin']);
        $this->assertEquals('0004', $card['last4']);
        $this->assertNotEmpty($card['uuid']);

        $card = $customerCards['cards'][1];
        $this->assertEquals('Antonio Garcia', $card['holder']);
        $this->assertEquals('CARD', $card['object']);
        $this->assertEquals('454881', $card['bin']);
        $this->assertEquals('0004', $card['last4']);
        $this->assertNotEmpty($card['uuid']);

        return $this->customerExternalId;
    }

    /**
     * @depends testRetrieveCustomerCardsWithDefaultOptionsValidatedCardsAndNotUnique
     */
    public function testRetrieveCustomerCardsAllNotUnique($customerExternalId)
    {
        $customerCards = $this->client->retrieveCustomerCards($customerExternalId, 'ALL', 'false');

        $this->assertSame('OK', $customerCards['message']);
        $this->assertSame(200, $customerCards['code']);
        $this->assertCount(3, $customerCards['cards']);
        $card = $customerCards['cards'][2];
        $this->assertEquals('CARD', $card['object']);
        $this->assertEquals('411111', $card['bin']);
        $this->assertEquals('1111', $card['last4']);
        $this->assertNotEmpty($card['uuid']);
    }

    /**
     * @depends testRetrieveCustomerCardsWithDefaultOptionsValidatedCardsAndNotUnique
     */
    public function testRetrieveCustomerCardsAllUnique($customerExternalId)
    {
        $customerCards = $this->client->retrieveCustomerCards($customerExternalId, 'ALL', 'true');

        $this->assertSame('OK', $customerCards['message']);
        $this->assertSame(200, $customerCards['code']);
        $this->assertCount(2, $customerCards['cards']);
    }

    /**
     * @depends testRetrieveCustomerCardsWithDefaultOptionsValidatedCardsAndNotUnique
     */
    public function testRetrieveCustomerCardsValidatedUnique($customerExternalId)
    {
        $customerCards = $this->client->retrieveCustomerCards($customerExternalId, 'VALIDATED', 'true');

        $this->assertSame('OK', $customerCards['message']);
        $this->assertSame(200, $customerCards['code']);
        $this->assertCount(1, $customerCards['cards']);
    }
}
