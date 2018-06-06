<?php

namespace WAM\Paylands\Tests\Integration;


use WAM\Paylands\Client;
use WAM\Paylands\ClientFactory;
use WAM\Paylands\ClientInterface;
use WAM\Paylands\DiscoveryProxy;
use WAM\Paylands\RequestFactory;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testSaveCard()
    {
        if (!$_ENV['ENABLE_API_INTEGRATION']) {
            $this->markTestSkipped('Api integration disabled');

            return;
        }

        $apiDiscoveryProxy = new DiscoveryProxy();

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxy,
            $_ENV['API_SIGNATURE']
        );

        $apiRequestFactory->setRequestFactory();

        $clientFactory = new ClientFactory(
            $apiRequestFactory,
            $apiDiscoveryProxy,
            $_ENV['API_KEY'],
            $_ENV['API_URL'],
            $_ENV['API_SANDBOX']
        );

        $clientFactory->setUriFactory();
        $clientFactory->setHttpClient();

        /** @var ClientInterface $client */
        $client = $clientFactory->create();

        $card = $client->saveCard(
            '123',
            'Jonh Doe',
            '4111111111111111',
            '21',
            '12',
            '987'
        );

        $this->assertSame('OK', $card['message']);
        $this->assertSame(200, $card['code']);
        $this->assertSame('123', $card['Customer']['external_id']);
        $this->assertSame('Jonh Doe', $card['Source']['holder']);
        $this->assertEquals(411111, $card['Source']['bin']);
        $this->assertEquals(1111, $card['Source']['last4']);
        $this->assertSame('12', $card['Source']['expire_month']);
        $this->assertSame('21', $card['Source']['expire_year']);
        $this->assertNotEmpty($card['Source']['uuid']);
    }

    public function testSaveCardWithServiceValidation()
    {
        if (!$_ENV['ENABLE_API_INTEGRATION']) {
            $this->markTestSkipped('Api integration disabled');

            return;
        }

        $apiDiscoveryProxy = new DiscoveryProxy();

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxy,
            $_ENV['API_SIGNATURE']
        );

        $apiRequestFactory->setRequestFactory();

        $clientFactory = new ClientFactory(
            $apiRequestFactory,
            $apiDiscoveryProxy,
            $_ENV['API_KEY'],
            $_ENV['API_URL'],
            $_ENV['API_SANDBOX']
        );

        $clientFactory->setUriFactory();
        $clientFactory->setHttpClient();

        /** @var ClientInterface $client */
        $client = $clientFactory->create();

        $card = $client->saveCard(
            '123',
            'Jonh Doe',
            '4548812049400004',
            '20',
            '12',
            '987',
            true,
            $_ENV['API_PAYMENT_SERVICE']
        );

        $this->assertSame('OK', $card['message']);
        $this->assertSame(200, $card['code']);
        $this->assertSame('123', $card['Customer']['external_id']);
        $this->assertSame('Jonh Doe', $card['Source']['holder']);
        $this->assertSame('454881', $card['Source']['bin']);
        $this->assertSame('0004', $card['Source']['last4']);
        $this->assertSame('12', $card['Source']['expire_month']);
        $this->assertSame('20', $card['Source']['expire_year']);
        $this->assertNotEmpty($card['Source']['uuid']);
    }
}
