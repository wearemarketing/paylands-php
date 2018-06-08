<?php

namespace WAM\Paylands;

/**
 * Interface ClientInterface.
 *
 * @author Santi Garcia <sgarcia@wearemarketing.com>, <sangarbe@gmail.com>
 */
interface ClientInterface
{
    const OPERATIVE_AUTHORIZATION = 'AUTHORIZATION';
    const OPERATIVE_DEFERRED = 'DEFERRED';
    const OPERATIVE_REFUND = 'REFUND';
    const OPERATIVE_CONFIRMATION = 'CONFIRMATION';
    const OPERATIVE_CANCELLATION = 'CANCELLATION';

    const CARD_STATUS_VALIDATED = 'VALIDATED';
    const CARD_STATUS_ALL = 'ALL';
    const CARD_UNIQUE = 'true';
    const CARD_NOT_UNIQUE = 'false';

    /**
     * Gets if client is in sandbox mode.
     *
     * @return bool
     */
    public function isModeSandboxEnabled();

    /**
     * Gets current client's operative.
     *
     * @return string
     */
    public function getOperative();

    /**
     * Gets current template uuid to use to capture card.
     *
     * @return string
     */
    public function getTemplate();

    /**
     * Requests Paylands API to create a new customer.
     *
     * @param int $customerExtId Customer external id to map to application
     *
     * @return array
     */
    public function createCustomer($customerExtId);

    /**
     * Requests Paylands API to retrieve tokenized cards of a customer.
     *
     * @param string $customerExtId Customer external id
     * @param string $status
     * @param string $unique
     *
     * @return array
     */
    public function retrieveCustomerCards($customerExtId, $status, $unique);

    /**
     * Requests Paylands API to create a new payment order.
     *
     * @param string $customerExtId
     * @param int    $amount
     * @param string $description
     * @param string $service
     *
     * @return array
     */
    public function createPayment($customerExtId, $amount, $description, $service);

    /**
     * Requests Paylands API to pay a previously created order.
     *
     * @param string $ip
     * @param string $orderUuid
     * @param string $cardUuid
     *
     * @return array
     */
    public function directPayment($ip, $orderUuid, $cardUuid);

    /**
     * Requests Paylands API to refund (totally or partially) a previously paid order.
     *
     * @param string $orderUuid
     * @param int    $amount
     *
     * @return array
     */
    public function refundPayment($orderUuid, $amount = null);

    /**
     * Requests Paylands API to confirm a previously created 'deferred' order.
     *
     * @param string $orderUuid
     *
     * @return array
     */
    public function confirmPayment($orderUuid);

    /**
     * Requests Paylands API to cancel a previously created 'deferred' order.
     *
     * @param string $orderUuid
     *
     * @return array
     */
    public function cancelPayment($orderUuid);

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
    );
}
