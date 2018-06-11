<?php

namespace WAM\Paylands\Tests;

use WAM\Paylands\Client;
use WAM\Paylands\ClientFactory;
use WAM\Paylands\DiscoveryProxy;
use WAM\Paylands\RequestFactory;

abstract class ClientBaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $customerExternalId;

    /**
     * @var string
     */
    protected $apiPaymentServiceId;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $enableIntegrationTests = '1' == getenv('ENABLE_API_INTEGRATION') ? true : false;
        $apiSandboxMode = '1' == getenv('API_SANDBOX') ? true : false;

        if (!$enableIntegrationTests) {
            $this->markTestSkipped('Api integration disabled');
        }

        $apiDiscoveryProxy = new DiscoveryProxy();

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxy,
            getenv('API_SIGNATURE')
        );

        $apiRequestFactory->setRequestFactory();

        $clientFactory = new ClientFactory(
            $apiRequestFactory,
            $apiDiscoveryProxy,
            getenv('API_KEY'),
            getenv('API_URL'),
            $apiSandboxMode
        );

        $clientFactory->setUriFactory();
        $clientFactory->setHttpClient();

        $this->client = $clientFactory->create();

        $this->customerExternalId = uniqid('php_');

        $this->apiPaymentServiceId = getenv('API_PAYMENT_SERVICE');
    }

    public function expectException($exception)
    {
        if (method_exists('\PHPUnit_Framework_TestCase', 'expectException')) {
            parent::expectException($exception);
        }

        parent::setExpectedException($exception);
    }
}
