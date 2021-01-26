<?php

namespace WAM\Paylands\Tests\Integration;

use WAM\Paylands\ClientInterface;
use WAM\Paylands\ErrorException;
use WAM\Paylands\Tests\ClientBaseTestCase;

class ClientRefundPaymentTest extends ClientBaseTestCase
{
    public function testTotalRefundPaymentSuccess()
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

        $this->client->directPayment('123.123.123.123', $orderUuid, $cardUuid);

        $refund = $this->client->refundPayment($orderUuid);

        $this->assertSame('OK', $refund['message']);
        $this->assertSame(200, $refund['code']);
        $this->assertSame(100, $refund['order']['refunded']);
        $this->assertCount(2, $refund['order']['transactions']);
        $this->assertSame('REFUND', $refund['order']['transactions'][1]['operative']);
        $this->assertSame('SUCCESS', $refund['order']['transactions'][1]['status']);
    }

    public function testPartialRefundPaymentSuccess()
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

        $this->client->directPayment('123.123.123.123', $orderUuid, $cardUuid);

        $refund = $this->client->refundPayment($orderUuid, 50);

        $this->assertSame('OK', $refund['message']);
        $this->assertSame(200, $refund['code']);
        $this->assertTrue($refund['order']['paid']);
        $this->assertSame(50, $refund['order']['refunded']);
        $this->assertCount(2, $refund['order']['transactions']);
        $this->assertSame('REFUND', $refund['order']['transactions'][1]['operative']);
        $this->assertSame('SUCCESS', $refund['order']['transactions'][1]['status']);

        return $orderUuid;
    }

    public function testRefundPaymentThrowsExceptionWhenRefundAmountGreaterThanPaidAmount()
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

        $this->client->directPayment('123.123.123.123', $orderUuid, $cardUuid);

        $this->expectException(ErrorException::class);

        $this->client->refundPayment($orderUuid, 101);
    }

    public function testPartialRefundPaymentThrowsExceptionWhenRefundAmountGreaterThanPaidAmount()
    {
        $orderUuid = $this->testPartialRefundPaymentSuccess();

        $this->expectException(ErrorException::class);

        $this->client->refundPayment($orderUuid, 51);
    }

    public function testRefundPaymentWhenNegativeAmountIsRefused()
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

        $this->client->directPayment('123.123.123.123', $orderUuid, $cardUuid);

	    $this->expectException(ErrorException::class);

        $this->client->refundPayment($orderUuid, -100);
    }

    public function testRefundPaymentWorksForDeferredPaymentWhichHasBeenConfirmed()
    {
        $this->client->setOperative(ClientInterface::OPERATIVE_DEFERRED);

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

        $deferredPayment = $this->client->directPayment('123.123.123.123', $orderUuid, $cardUuid);

        $this->assertSame('OK', $deferredPayment['message']);
        $this->assertSame(200, $deferredPayment['code']);
        $this->assertCount(1, $deferredPayment['order']['transactions']);
        $this->assertSame('SUCCESS', $deferredPayment['order']['transactions'][0]['status']);
        $this->assertSame('DEFERRED', $deferredPayment['order']['transactions'][0]['operative']);

        $confirmation = $this->client->confirmPayment($orderUuid);

        $this->assertSame('OK', $confirmation['message']);
        $this->assertSame(200, $confirmation['code']);
        $this->assertTrue($confirmation['order']['paid']);
        $this->assertCount(2, $confirmation['order']['transactions']);
        $this->assertSame('SUCCESS', $confirmation['order']['transactions'][1]['status']);
        $this->assertSame('CONFIRMATION', $confirmation['order']['transactions'][1]['operative']);

        $refund = $this->client->refundPayment($orderUuid);

        $this->assertSame('OK', $refund['message']);
        $this->assertSame(200, $refund['code']);
        $this->assertTrue($refund['order']['paid']);
        $this->assertCount(3, $refund['order']['transactions']);
        $this->assertSame('SUCCESS', $refund['order']['transactions'][2]['status']);
        $this->assertSame('REFUND', $refund['order']['transactions'][2]['operative']);
    }

    public function testRefundPaymentThrowsExceptionForUnconfirmedDeferredPayment()
    {
        $this->client->setOperative(ClientInterface::OPERATIVE_DEFERRED);

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

        $deferredPayment = $this->client->directPayment('123.123.123.123', $orderUuid, $cardUuid);

        $this->assertSame('OK', $deferredPayment['message']);
        $this->assertSame(200, $deferredPayment['code']);
        $this->assertCount(1, $deferredPayment['order']['transactions']);
        $this->assertSame('SUCCESS', $deferredPayment['order']['transactions'][0]['status']);
        $this->assertSame('DEFERRED', $deferredPayment['order']['transactions'][0]['operative']);

        $this->expectException(ErrorException::class);

        $this->client->refundPayment($orderUuid);
    }
}
