<?php

namespace WAM\Paylands\Tests\Integration;

use WAM\Paylands\Tests\ClientBaseTestCase;

class ClientSaveCardTest extends ClientBaseTestCase
{
    public function testSaveCard()
    {
        $card = $this->client->saveCard(
            $this->customerExternalId,
            'Jonh Doe',
            '4548812049400004',
            '20',
            '12',
            '987'
        );

        $this->assertSame('OK', $card['message']);
        $this->assertSame(200, $card['code']);
        $this->assertSame($this->customerExternalId, $card['Customer']['external_id']);
        $this->assertSame('Jonh Doe', $card['Source']['holder']);
        $this->assertEquals('454881', $card['Source']['bin']);
        $this->assertEquals('0004', $card['Source']['last4']);
        $this->assertSame('12', $card['Source']['expire_month']);
        $this->assertSame('20', $card['Source']['expire_year']);
        $this->assertNotEmpty($card['Source']['uuid']);
    }

    public function testSaveCardWithServiceValidation()
    {
        $card = $this->client->saveCard(
            $this->customerExternalId,
            'Jonh Doe',
            '4548812049400004',
            '20',
            '12',
            '987',
            true,
            $this->apiPaymentServiceId
        );

        $this->assertSame('OK', $card['message']);
        $this->assertSame(200, $card['code']);
        $this->assertSame($this->customerExternalId, $card['Customer']['external_id']);
        $this->assertSame('Jonh Doe', $card['Source']['holder']);
        $this->assertEquals('454881', $card['Source']['bin']);
        $this->assertEquals('0004', $card['Source']['last4']);
        $this->assertSame('12', $card['Source']['expire_month']);
        $this->assertSame('20', $card['Source']['expire_year']);
        $this->assertNotEmpty($card['Source']['uuid']);
    }
}
