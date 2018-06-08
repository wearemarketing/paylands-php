<?php

namespace WAM\Paylands\Tests\Integration;

use WAM\Paylands\Tests\ClientBaseTestCase;

class ClientCreateCustomerTest extends ClientBaseTestCase
{
    public function testCreateCustomer()
    {
        $customer = $this->client->createCustomer($this->customerExternalId);

        $this->assertSame('OK', $customer['message']);
        $this->assertSame(200, $customer['code']);
        $this->assertSame($this->customerExternalId, $customer['Customer']['external_id']);
        $this->assertNotEmpty($customer['Customer']['token']);
    }
}
