<?php

namespace WAM\Paylands\Tests;

use Http\Message\RequestFactory as HttpRequestFactory;
use WAM\Paylands\DiscoveryProxy;
use WAM\Paylands\RequestFactory;
use Psr\Http\Message\RequestInterface;

/**
 * Class RequestFactoryTest.
 *
 * @author Santi Garcia <sgarcia@wearemarketing.com>, <sangarbe@gmail.com>
 */
class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function trySetRequestFactory()
    {
        $apiRequestFactory = new RequestFactoryTestClass(
            $this->prophesize(DiscoveryProxy::class)->reveal(),
            'signature'
        );

        $requestFactoryMock = $this->prophesize(HttpRequestFactory::class);

        $apiRequestFactory->setRequestFactory($requestFactoryMock->reveal());

        $this->assertSame($requestFactoryMock->reveal(), $apiRequestFactory->getRequestFactory());
    }

    /**
     * @test
     */
    public function trySetRequestFactoryWithDiscovery()
    {
        $requestFactoryMock = $this->prophesize(HttpRequestFactory::class);

        $apiDiscoveryProxyMock = $this->prophesize(DiscoveryProxy::class);
        $apiDiscoveryProxyMock
            ->discoverRequestFactory()
            ->shouldBeCalled()
            ->willReturn($requestFactoryMock->reveal());

        $apiRequestFactory = new RequestFactoryTestClass(
            $apiDiscoveryProxyMock->reveal(),
            'signature'
        );

        $apiRequestFactory->setRequestFactory();

        $this->assertSame($requestFactoryMock->reveal(), $apiRequestFactory->getRequestFactory());
    }

    /**
     * @test
     */
    public function tryCreatePaymentRequest()
    {
        $data = [
            'method' => 'POST',
            'resource' => '/payment',
            'body' => [
                'customer_ext_id' => '123A',
                'amount' => 15,
                'operative' => 'AUTHORIZATION',
                'service' => 'service-id',
                'description' => 'empty',
                'signature' => 'abcd',
            ],
        ];

        $requestMock = $this->prophesize(RequestInterface::class);

        $requestFactoryMock = $this->getRequestFactoryMock($requestMock->reveal(), $data);

        $apiDiscoveryProxyMock = $this->getApiDiscoveryProxyMock($requestFactoryMock->reveal());

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxyMock->reveal(),
            $data['body']['signature']
        );

        $apiRequestFactory->setRequestFactory();

        $request = $apiRequestFactory
            ->setRequestFactory()
            ->createPaymentRequest(
                $data['body']['customer_ext_id'],
                $data['body']['amount'],
                $data['body']['description'],
                $data['body']['operative'],
                $data['body']['service']
            );

        $this->assertSame($request, $requestMock->reveal());
    }

    /**
     * @test
     */
    public function tryCreateCustomerRequest()
    {
        $data = [
            'method' => 'POST',
            'resource' => '/customer',
            'body' => [
                'customer_ext_id' => '123A',
                'signature' => 'abcd',
            ],
        ];

        $requestMock = $this->prophesize(RequestInterface::class);

        $requestFactoryMock = $this->getRequestFactoryMock($requestMock->reveal(), $data);

        $apiDiscoveryProxyMock = $this->getApiDiscoveryProxyMock($requestFactoryMock->reveal());

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxyMock->reveal(),
            $data['body']['signature']
        );

        $apiRequestFactory->setRequestFactory();

        $request = $apiRequestFactory
            ->setRequestFactory()
            ->createCustomerRequest($data['body']['customer_ext_id']);

        $this->assertSame($request, $requestMock->reveal());
    }

    /**
     * @test
     */
    public function tryCreateCustomerCardsRequest()
    {
        $data = [
            'method' => 'GET',
            'resource' => '/customer/123A/cards',
            'body' => [],
        ];

        $requestMock = $this->prophesize(RequestInterface::class);

        $requestFactoryMock = $this->getRequestFactoryMock($requestMock->reveal(), $data);

        $apiDiscoveryProxyMock = $this->getApiDiscoveryProxyMock($requestFactoryMock->reveal());

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxyMock->reveal(),
            'signature'
        );

        $apiRequestFactory->setRequestFactory();

        $request = $apiRequestFactory
            ->setRequestFactory()
            ->createCustomerCardsRequest('123A');

        $this->assertSame($request, $requestMock->reveal());
    }

    /**
     * @test
     */
    public function tryCreateDirectPaymentRequest()
    {
        $data = [
            'method' => 'POST',
            'resource' => '/payment/direct',
            'body' => [
                'customer_ip' => '192.168.0.1',
                'order_uuid' => 'O-123',
                'card_uuid' => 'C-123',
                'signature' => 'abcd',
            ],
        ];

        $requestMock = $this->prophesize(RequestInterface::class);

        $requestFactoryMock = $this->getRequestFactoryMock($requestMock->reveal(), $data);

        $apiDiscoveryProxyMock = $this->getApiDiscoveryProxyMock($requestFactoryMock->reveal());

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxyMock->reveal(),
            $data['body']['signature']
        );

        $apiRequestFactory->setRequestFactory();

        $request = $apiRequestFactory
            ->setRequestFactory()
            ->createDirectPaymentRequest(
                $data['body']['customer_ip'],
                $data['body']['order_uuid'],
                $data['body']['card_uuid']
            );

        $this->assertSame($request, $requestMock->reveal());
    }

    /**
     * @test
     */
    public function tryCreateCancelPaymentRequest()
    {
        $data = [
            'method' => 'POST',
            'resource' => '/payment/cancellation',
            'body' => [
                'order_uuid' => 'O-123',
                'signature' => 'abcd',
            ],
        ];

        $requestMock = $this->prophesize(RequestInterface::class);

        $requestFactoryMock = $this->getRequestFactoryMock($requestMock->reveal(), $data);

        $apiDiscoveryProxyMock = $this->getApiDiscoveryProxyMock($requestFactoryMock->reveal());

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxyMock->reveal(),
            $data['body']['signature']
        );

        $apiRequestFactory->setRequestFactory();

        $request = $apiRequestFactory
            ->setRequestFactory()
            ->createCancelPaymentRequest($data['body']['order_uuid']);

        $this->assertSame($request, $requestMock->reveal());
    }

    /**
     * @test
     */
    public function tryCreateConfirmPaymentRequest()
    {
        $data = [
            'method' => 'POST',
            'resource' => '/payment/confirmation',
            'body' => [
                'order_uuid' => 'O-123',
                'signature' => 'abcd',
            ],
        ];

        $requestMock = $this->prophesize(RequestInterface::class);

        $requestFactoryMock = $this->getRequestFactoryMock($requestMock->reveal(), $data);

        $apiDiscoveryProxyMock = $this->getApiDiscoveryProxyMock($requestFactoryMock->reveal());

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxyMock->reveal(),
            $data['body']['signature']
        );

        $apiRequestFactory->setRequestFactory();

        $request = $apiRequestFactory
            ->setRequestFactory()
            ->createConfirmPaymentRequest($data['body']['order_uuid']);

        $this->assertSame($request, $requestMock->reveal());
    }

    /**
     * @test
     */
    public function tryCreateTotalRefundPaymentRequest()
    {
        $data = [
            'method' => 'POST',
            'resource' => '/payment/refund',
            'body' => [
                'order_uuid' => 'O-123',
                'signature' => 'abcd',
            ],
        ];

        $requestMock = $this->prophesize(RequestInterface::class);

        $requestFactoryMock = $this->getRequestFactoryMock($requestMock->reveal(), $data);

        $apiDiscoveryProxyMock = $this->getApiDiscoveryProxyMock($requestFactoryMock->reveal());

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxyMock->reveal(),
            $data['body']['signature']
        );

        $apiRequestFactory->setRequestFactory();

        $request = $apiRequestFactory
            ->setRequestFactory()
            ->createRefundPaymentRequest($data['body']['order_uuid']);

        $this->assertSame($request, $requestMock->reveal());
    }

    /**
     * @test
     */
    public function tryCreatePartialRefundPaymentRequest()
    {
        $data = [
            'method' => 'POST',
            'resource' => '/payment/refund',
            'body' => [
                'order_uuid' => 'O-123',
                'amount' => 15,
                'signature' => 'abcd',
            ],
        ];

        $requestMock = $this->prophesize(RequestInterface::class);

        $requestFactoryMock = $this->getRequestFactoryMock($requestMock->reveal(), $data);

        $apiDiscoveryProxyMock = $this->getApiDiscoveryProxyMock($requestFactoryMock->reveal());

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxyMock->reveal(),
            $data['body']['signature']
        );

        $apiRequestFactory->setRequestFactory();

        $request = $apiRequestFactory
            ->setRequestFactory()
            ->createRefundPaymentRequest($data['body']['order_uuid'], $data['body']['amount']);

        $this->assertSame($request, $requestMock->reveal());
    }

    /**
     * @test
     */
    public function tryCreateSaveCardRequest() {
        $data = [
            'method' => 'POST',
            'resource' => '/payment-method/card',
            'body' => [
                'customer_ext_id' => '123',
                'card_holder' => 'Jonh Doe',
                'card_pan' => '4111111111111111',
                'card_expiry_year' => '12',
                'card_expiry_month' => '12',
                'card_cvv' => '123',
                'validate' => true,
                'additional' => 'additional info for this card',
                'signature' => 'secret-signature',
                'service' => 'uuid-of-the-validation-service',
            ],
        ];

        $requestMock = $this->prophesize(RequestInterface::class);

        $requestFactoryMock = $this->getRequestFactoryMock($requestMock->reveal(), $data);

        $apiDiscoveryProxyMock = $this->getApiDiscoveryProxyMock($requestFactoryMock->reveal());

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxyMock->reveal(),
            $data['body']['signature']
        );

        $apiRequestFactory->setRequestFactory();

        $request = $apiRequestFactory
            ->setRequestFactory()
            ->createSaveCardRequest(
                $data['body']['customer_ext_id'],
                $data['body']['card_holder'],
                $data['body']['card_pan'],
                $data['body']['card_expiry_year'],
                $data['body']['card_expiry_month'],
                $data['body']['card_cvv'],
                $data['body']['validate'],
                $data['body']['service'],
                $data['body']['additional']
            );

        $this->assertSame($request, $requestMock->reveal());
    }

    /**
     * @test
     */
    public function tryCreateSaveCardDisableValidateRequest() {
        $data = [
            'method' => 'POST',
            'resource' => '/payment-method/card',
            'body' => [
                'customer_ext_id' => '123',
                'card_holder' => 'Jonh Doe',
                'card_pan' => '4111111111111111',
                'card_expiry_year' => '12',
                'card_expiry_month' => '12',
                'card_cvv' => '123',
                'validate' => false,
                'additional' => 'additional info for this card',
                'signature' => 'secret-signature',
            ],
        ];

        $requestMock = $this->prophesize(RequestInterface::class);

        $requestFactoryMock = $this->getRequestFactoryMock($requestMock->reveal(), $data);

        $apiDiscoveryProxyMock = $this->getApiDiscoveryProxyMock($requestFactoryMock->reveal());

        $apiRequestFactory = new RequestFactory(
            $apiDiscoveryProxyMock->reveal(),
            $data['body']['signature']
        );

        $apiRequestFactory->setRequestFactory();

        $request = $apiRequestFactory
            ->setRequestFactory()
            ->createSaveCardRequest(
                $data['body']['customer_ext_id'],
                $data['body']['card_holder'],
                $data['body']['card_pan'],
                $data['body']['card_expiry_year'],
                $data['body']['card_expiry_month'],
                $data['body']['card_cvv'],
                $data['body']['validate'],
                '',
                $data['body']['additional']
            );

        $this->assertSame($request, $requestMock->reveal());
    }

    /**
     * @param RequestInterface $request
     * @param array            $data
     *
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function getRequestFactoryMock(RequestInterface $request, array $data)
    {
        $requestFactoryMock = $this->prophesize(HttpRequestFactory::class);
        $requestFactoryMock
            ->createRequest($data['method'], $data['resource'], [], empty($data['body']) ? null : \json_encode($data['body']))
            ->shouldBeCalled()
            ->willReturn($request);

        return $requestFactoryMock;
    }

    /**
     * @param HttpRequestFactory $requestFactory
     *
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function getApiDiscoveryProxyMock(HttpRequestFactory $requestFactory)
    {
        $apiDiscoveryProxyMock = $this->prophesize(DiscoveryProxy::class);
        $apiDiscoveryProxyMock
            ->discoverRequestFactory()
            ->shouldBeCalled()
            ->willReturn($requestFactory);

        return $apiDiscoveryProxyMock;
    }
}

/**
 * Class RequestFactoryTestClass.
 *
 * @author Santi Garcia <sgarcia@wearemarketing.com>, <sangarbe@gmail.com>
 */
class RequestFactoryTestClass extends RequestFactory
{
    public function getRequestFactory()
    {
        return $this->requestFactory;
    }
}
