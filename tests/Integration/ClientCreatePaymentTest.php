<?php

namespace WAM\Paylands\Tests\Integration;

use WAM\Paylands\Tests\ClientBaseTestCase;

class ClientCreatePaymentTest extends ClientBaseTestCase
{
    public function testCreatePayment()
    {
        $payment = $this->client->createPayment(
            $this->customerExternalId,
            100,
            'Test payment order',
            $this->apiPaymentServiceId
        );
        var_dump($payment);
        $this->assertSame('OK', $payment['message']);
        $this->assertSame(200, $payment['code']);
        $this->assertFalse($payment['order']['paid']);
        $this->assertCount(0, $payment['order']['transactions']);
        $this->assertNotEmpty($payment['order']['uuid']);
        $this->assertSame(100, $payment['order']['amount']);
        $this->assertSame($this->customerExternalId, $payment['order']['customer']);
    }
}
