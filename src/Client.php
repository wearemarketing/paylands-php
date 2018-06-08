<?php

namespace WAM\Paylands;

use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;

/**
 * Class Client.
 *
 * @author Santi Garcia <sgarcia@wearemarketing.com>, <sangarbe@gmail.com>
 */
class Client implements ClientInterface
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var RequestFactory
     */
    protected $apiRequestFactory;

    /**
     * @var bool
     */
    protected $sandbox;

    /**
     * @var string
     */
    protected $operative = ClientInterface::OPERATIVE_AUTHORIZATION;

    /**
     * @var array
     */
    protected $i18nTemplates = [];

    /**
     * @var string
     */
    protected $fallbackTemplate = '';

    /**
     * Client constructor.
     *
     * @param HttpClient     $httpClient
     * @param RequestFactory $apiRequestFactory
     * @param bool           $sandbox
     */
    public function __construct(HttpClient $httpClient, RequestFactory $apiRequestFactory, $sandbox)
    {
        $this->httpClient = $httpClient;
        $this->apiRequestFactory = $apiRequestFactory;
        $this->sandbox = $sandbox;
    }

    /**
     * Gets if client is in sandbox mode.
     *
     * @return bool
     */
    public function isModeSandboxEnabled()
    {
        return $this->sandbox;
    }

    /**
     * Sets API's payments operative.
     *
     * @param string $operative
     *
     * @return ClientInterface
     */
    public function setOperative($operative)
    {
        $this->operative = $operative;

        return $this;
    }

    /**
     * Gets current client's operative.
     *
     * @return string
     */
    public function getOperative()
    {
        return $this->operative;
    }

    /**
     * Sets defined template uuids to use to capture card by locale, and sets the fallback one.
     *
     * @param array $fallback
     * @param array $i18n
     */
    public function setTemplates($fallback, array $i18n)
    {
        $this->i18nTemplates = $i18n;
        $this->fallbackTemplate = $fallback;
    }

    /**
     * Gets current template uuid to use to capture card.
     *
     * @return string
     */
    public function getTemplate($locale = null)
    {
        if (!$locale) {
            return $this->fallbackTemplate;
        }

        foreach ($this->i18nTemplates as $templateLocale => $template) {
            if (strtolower($templateLocale) === $locale) {
                return $template;
            }
        }

        return $this->fallbackTemplate;
    }

    /**
     * Requests Paylands API to create a new payment order.
     *
     * @param string $customerExtId
     * @param int    $amount
     * @param string $description
     * @param string $service
     *
     * @return array
     *
     * @throws ErrorException
     */
    public function createPayment($customerExtId, $amount, $description, $service)
    {
        $request = $this
            ->apiRequestFactory
            ->createPaymentRequest($customerExtId, $amount, $description, $this->getOperative(), $service);

        return $this->send($request);
    }

    /**
     * Requests Paylands API to create a new customer.
     *
     * @param int $customerExtId Customer external id to map to application
     *
     * @return array
     *
     * @throws ErrorException
     */
    public function createCustomer($customerExtId)
    {
        $request = $this
            ->apiRequestFactory
            ->createCustomerRequest($customerExtId);

        return $this->send($request);
    }

    /**
     * Requests Paylands API to retrieve tokenized cards of a customer.
     *
     * @param string $customerExtId Customer external id
     * @param string $status        Card status filter = ['VALIDATED', 'ALL']
     * @param string $unique        Whether return just one instance of the same card or every payment done with it
     *
     * @return array
     *
     * @throws ErrorException
     */
    public function retrieveCustomerCards(
        $customerExtId,
        $status = ClientInterface::CARD_STATUS_VALIDATED,
        $unique = ClientInterface::CARD_UNIQUE
    ) {
        $request = $this
            ->apiRequestFactory
            ->createCustomerCardsRequest($customerExtId, $status, $unique);

        return $this->send($request);
    }

    /**
     * Requests Paylands API to pay a previously created order.
     *
     * @param string $ip
     * @param string $orderUuid
     * @param string $cardUuid
     *
     * @return array
     *
     * @throws ErrorException
     */
    public function directPayment($ip, $orderUuid, $cardUuid)
    {
        $request = $this
            ->apiRequestFactory
            ->createDirectPaymentRequest($ip, $orderUuid, $cardUuid);

        return $this->send($request);
    }

    /**
     * Requests Paylands API to refund (totally or partially) a previously paid order.
     *
     * @param string $orderUuid
     * @param int    $amount
     *
     * @return array
     *
     * @throws ErrorException
     */
    public function refundPayment($orderUuid, $amount = null)
    {
        $request = $this
            ->apiRequestFactory
            ->createRefundPaymentRequest($orderUuid, $amount);

        return $this->send($request);
    }

    /**
     * Requests Paylands API to confirm a previously created 'deferred' order.
     *
     * @param string $orderUuid
     *
     * @return array
     *
     * @throws ErrorException
     */
    public function confirmPayment($orderUuid)
    {
        $request = $this
            ->apiRequestFactory
            ->createConfirmPaymentRequest($orderUuid);

        return $this->send($request);
    }

    /**
     * Requests Paylands API to cancel a previously created 'deferred' order.
     *
     * @param string $orderUuid
     *
     * @return array
     *
     * @throws ErrorException
     */
    public function cancelPayment($orderUuid)
    {
        $request = $this
            ->apiRequestFactory
            ->createCancelPaymentRequest($orderUuid);

        return $this->send($request);
    }

    /**
     * Requests Paylands API to save a card for a customer.
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
     * @return array
     *
     * @throws ErrorException
     */
    public function saveCard(
        $customerExtId,
        $cardHolder,
        $cardPan,
        $cardExpiryYear,
        $cardExpiryMonth,
        $cardCVV,
        $validate = false,
        $service = '',
        $additional = ''
    ) {
        $request = $this
            ->apiRequestFactory
            ->createSaveCardRequest($customerExtId,
                $cardHolder,
                $cardPan,
                $cardExpiryYear,
                $cardExpiryMonth,
                $cardCVV,
                $validate,
                $service,
                $additional
            );

        return $this->send($request);
    }

    /**
     * @param RequestInterface $request
     *
     * @return array
     *
     * @throws ErrorException
     */
    protected function send(RequestInterface $request)
    {
        try {
            $response = $this->httpClient->sendRequest($request);

            $body = (string) $response->getBody();

            return \json_decode($body, true);
        } catch (\Exception $e) {
            throw new ErrorException(sprintf('There was an error requesting Paylands API: %s', $e->getMessage()));
        }
    }
}
