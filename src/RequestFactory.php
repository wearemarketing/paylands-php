<?php

namespace WAM\Paylands;

use Http\Message\RequestFactory as HttpRequestFactory;
use Psr\Http\Message\RequestInterface;

/**
 * Class RequestFactory.
 *
 * @author Santi Garcia <sgarcia@wearemarketing.com>, <sangarbe@gmail.com>
 */
class RequestFactory
{
    /**
     * @var string
     */
    protected $apiSignature;

    /**
     * @var HttpRequestFactory
     */
    protected $requestFactory;

    /**
     * @var DiscoveryProxy
     */
    protected $apiDiscoveryProxy;

    /**
     * RequestFactory constructor.
     *
     * @param string         $apiSignature
     * @param DiscoveryProxy $apiDiscoveryProxy
     */
    public function __construct(
        DiscoveryProxy $apiDiscoveryProxy,
        $apiSignature
    ) {
        $this->apiDiscoveryProxy = $apiDiscoveryProxy;
        $this->apiSignature = $apiSignature;
    }

    /**
     * Sets factory for PSR-7 requests.
     *
     * @param HttpRequestFactory $requestFactory
     *
     * @return $this
     */
    public function setRequestFactory(HttpRequestFactory $requestFactory = null)
    {
        if (!$requestFactory) {
            $requestFactory = $this->apiDiscoveryProxy->discoverRequestFactory();
        }

        $this->requestFactory = $requestFactory;

        return $this;
    }

    /**
     * Returns a PSR-7 request to create a payment order into Paylands.
     *
     * @param $customerExtId
     * @param $amount
     * @param $description
     * @param $operative
     * @param $service
     *
     * @return RequestInterface
     */
    public function createPaymentRequest($customerExtId, $amount, $description, $operative, $service)
    {
        return $this->createRequest('POST', '/payment', [
            'customer_ext_id' => (string) $customerExtId,
            'amount' => $amount,
            'operative' => $operative,
            'service' => $service,
            'description' => $description,
        ]);
    }

    /**
     * Returns a PSR-7 request to create a customer into Paylands.
     *
     * @param $customerExtId
     *
     * @return RequestInterface
     */
    public function createCustomerRequest($customerExtId)
    {
        return $this->createRequest('POST', '/customer', [
            'customer_ext_id' => (string) $customerExtId,
        ]);
    }

    /**
     * Returns a PSR-7 request to retrieve customer's cards from Paylands.
     *
     * @param $customerExtId
     *
     * @return RequestInterface
     */
    public function createCustomerCardsRequest($customerExtId)
    {
        return $this->createRequest('GET', sprintf('/customer/%s/cards', $customerExtId));
    }

    /**
     * Returns a PSR-7 request to create a direct payment into Paylands.
     *
     * @param $ip
     * @param $orderUuid
     * @param $cardUuid
     *
     * @return RequestInterface
     */
    public function createDirectPaymentRequest($ip, $orderUuid, $cardUuid)
    {
        return $this->createRequest('POST', '/payment/direct', [
            'customer_ip' => $ip,
            'order_uuid' => $orderUuid,
            'card_uuid' => $cardUuid,
        ]);
    }

    /**
     * Returns a PSR-7 request to create a refund of a payment into Paylands.
     *
     * @param $orderUuid
     * @param null $amount
     *
     * @return RequestInterface
     */
    public function createRefundPaymentRequest($orderUuid, $amount = null)
    {
        $amountData = is_null($amount) ? [] : [
            'amount' => $amount,
        ];

        return $this->createRequest('POST', '/payment/refund', [
                'order_uuid' => $orderUuid,
            ] + $amountData);
    }

    /**
     * Returns a PSR-7 request to confirm a deferred payment into Paylands.
     *
     * @param $orderUuid
     *
     * @return RequestInterface
     */
    public function createConfirmPaymentRequest($orderUuid)
    {
        return $this->createRequest('POST', '/payment/confirmation', [
            'order_uuid' => $orderUuid,
        ]);
    }

    /**
     * Returns a PSR-7 request to cancel a deffered payment into Paylands.
     *
     * @param $orderUuid
     *
     * @return RequestInterface
     */
    public function createCancelPaymentRequest($orderUuid)
    {
        return $this->createRequest('POST', '/payment/cancellation', [
            'order_uuid' => $orderUuid,
        ]);
    }

    /**
     * Returns a PSR-7 request to save a credit card into Paylands.
     *
     * @param string $customerExtId
     * @param string $cardHolder
     * @param string $cardPan
     * @param string $cardExpiryYear
     * @param string $cardExpiryMonth
     * @param string $cardCVV
     * @param bool   $validate
     * @param string $service
     * @param string $additional
     *
     * @return RequestInterface
     */
    public function createSaveCardRequest(
        $customerExtId,
        $cardHolder,
        $cardPan,
        $cardExpiryYear,
        $cardExpiryMonth,
        $cardCVV,
        $validate,
        $service,
        $additional
    ) {
        $validationService = !$validate ? [] : [
            'service' => $service,
        ];

        return $this->createRequest('POST', '/payment-method/card', [
            'customer_ext_id' => $customerExtId,
            'card_holder' => $cardHolder,
            'card_pan' => $cardPan,
            'card_expiry_year' => $cardExpiryYear,
            'card_expiry_month' => $cardExpiryMonth,
            'card_cvv' => $cardCVV,
            'validate' => $validate,
            'additional' => $additional,
            'signature' => $this->apiSignature,
        ] + $validationService);
    }

    /**
     * Uses a PSR-7 compliant request factory to build up the expected request.
     *
     * @param string $method   Http method (POST, GET)
     * @param string $resource Uri path sufix
     * @param array  $data     Request's body data to send
     *
     * @return RequestInterface
     */
    private function createRequest($method, $resource, array $data = [])
    {
        if (!empty($data)) {
            $data['signature'] = $this->apiSignature;
        }

        return $this->requestFactory->createRequest($method, $resource, [], $this->encode($data));
    }

    /**
     * Encodes request body data as expected JSON.
     *
     * @param array $data
     *
     * @return string
     */
    private function encode(array $data)
    {
        if (empty($data)) {
            return null;
        }

        return \json_encode($data);
    }
}
