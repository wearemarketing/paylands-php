<?php

namespace WAM\Paylands\Tests\Unit;

use Http\Message\UriFactory;
use Http\Mock\Client;
use WAM\Paylands\ClientFactory;
use WAM\Paylands\ClientInterface;
use WAM\Paylands\DiscoveryProxy;
use WAM\Paylands\RequestFactory;
use Psr\Http\Message\UriInterface;

/**
 * Class ClientFactoryTest.
 *
 * @author Santi Garcia <sgarcia@wearemarketing.com>, <sangarbe@gmail.com>
 */
class ClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function trySetHttpClient()
    {
        $apiClientFactory = new ClientFactoryTestClass(
            $this->prophesize(RequestFactory::class)->reveal(), $this->prophesize(DiscoveryProxy::class)->reveal(), 'api-key', 'api-url'
        );

        $httpClientMock = $this->prophesize(Client::class);

        $apiClientFactory->setHttpClient($httpClientMock->reveal());

        $this->assertSame($httpClientMock->reveal(), $apiClientFactory->getHttpClient());
    }

    /**
     * @test
     */
    public function trySetHttpClientWithDiscovery()
    {
        $httpClientMock = $this->prophesize(Client::class);

        $apiDiscoveryProxyMock = $this->prophesize(DiscoveryProxy::class);
        $apiDiscoveryProxyMock
            ->discoverHttpClient()
            ->shouldBeCalled()
            ->willReturn($httpClientMock->reveal());

        $apiClientFactory = new ClientFactoryTestClass(
            $this->prophesize(RequestFactory::class)->reveal(), $apiDiscoveryProxyMock->reveal(), 'api-key', 'api-url'
        );

        $apiClientFactory->setHttpClient();

        $this->assertSame($httpClientMock->reveal(), $apiClientFactory->getHttpClient());
    }

    /**
     * @test
     */
    public function trySetUriFactory()
    {
        $apiClientFactory = new ClientFactoryTestClass(
            $this->prophesize(RequestFactory::class)->reveal(), $this->prophesize(DiscoveryProxy::class)->reveal(), 'api-key', 'api-url'
        );

        $uriFactoryMock = $this->prophesize(UriFactory::class);

        $apiClientFactory->setUriFactory($uriFactoryMock->reveal());

        $this->assertSame($uriFactoryMock->reveal(), $apiClientFactory->getUriFactory());
    }

    /**
     * @test
     */
    public function trySetUriFactoryWithDiscovery()
    {
        $uriFactoryMock = $this->prophesize(UriFactory::class);

        $apiDiscoveryProxyMock = $this->prophesize(DiscoveryProxy::class);
        $apiDiscoveryProxyMock
            ->discoverUriFactory()
            ->shouldBeCalled()
            ->willReturn($uriFactoryMock->reveal());

        $apiClientFactory = new ClientFactoryTestClass(
            $this->prophesize(RequestFactory::class)->reveal(), $apiDiscoveryProxyMock->reveal(), 'api-key', 'api-url'
        );

        $apiClientFactory->setUriFactory();

        $this->assertSame($uriFactoryMock->reveal(), $apiClientFactory->getUriFactory());
    }

    /**
     * @test
     */
    public function tryCreteApiClient()
    {
        $apiClientFactory = new ClientFactoryTestClass(
            $this->prophesize(RequestFactory::class)->reveal(), $this->prophesize(DiscoveryProxy::class)->reveal(), 'api-key', 'api-url'
        );
        $uriMock = $this->prophesize(UriInterface::class);
        $uriMock
            ->getPath()
            ->shouldBeCalled()
            ->willReturn("api-url");
        $uriMock
            ->getHost()
            ->shouldBeCalled()
            ->willReturn("host");

        $uriFactoryMock = $this->prophesize(UriFactory::class);
        $uriFactoryMock
            ->createUri('api-url')
            ->shouldBeCalled()
            ->willReturn($uriMock->reveal());

        $httpClientMock = $this->prophesize(Client::class);

        $apiClient = $apiClientFactory
            ->setUriFactory($uriFactoryMock->reveal())
            ->setHttpClient($httpClientMock->reveal())
            ->create();

        $this->assertInstanceOf(ClientInterface::class, $apiClient);
    }
}

/**
 * Class ClientFactoryTestClass.
 *
 * @author Santi Garcia <sgarcia@wearemarketing.com>, <sangarbe@gmail.com>
 */
class ClientFactoryTestClass extends ClientFactory
{
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    public function getUriFactory()
    {
        return $this->uriFactory;
    }
}
