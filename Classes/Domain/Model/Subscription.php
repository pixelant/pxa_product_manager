<?php

namespace Pixelant\PxaProductManager\Domain\Model;

/**
 * Class Subscription
 * @package Pixelant\PxaProductManager\Domain\Model
 */
class Subscription extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    // Subscription statuses
    const STATUS_ACTIVE = 1;
    const STATUS_PAUSED = 2;
    const STATUS_CANCELED = 3;

    // Subscription periods
    const RECURRING_FOR_WEEK = 1;
    const RECURRING_FOR_MONTH = 2;

    // Renew status
    const RENEW_STATUS_SUCCESS = 'success';

    // Time modifiers
    const WEEK_TIME_MODIFIER = '+5 days';
    const MONTH_TIME_MODIFIER = '+1 months';

    /**
     * $paymentDate
     *
     * @var \DateTime
     */
    protected $renewDate = null;

    /**
     * $paymentNextTry
     *
     * @var \DateTime
     */
    protected $nextTry = null;

    /**
     * @var int|null
     */
    protected $status = null;

    /**
     * $paymentDone
     *
     * @var string
     */
    protected $lastRenewStatus = null;

    /**
     * $attemptsLeft
     *
     * @var int
     */
    protected $attemptsLeft = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Order>
     */
    protected $orders;

    /**
     * @var string
     */
    protected $serializedProductsQuantity = '';

    /**
     * @var int
     */
    protected $subscriptionPeriod = 1;

    /**
     * Subscription constructor.
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * initStorageObjects
     */
    protected function initStorageObjects()
    {
        $this->orders = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @return \DateTime
     */
    public function getRenewDate(): \DateTime
    {
        return $this->renewDate;
    }

    /**
     * @param \DateTime $renewDate
     * @return Subscription
     */
    public function setRenewDate(\DateTime $renewDate): Subscription
    {
        $this->renewDate = $renewDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getNextTry(): \DateTime
    {
        return $this->nextTry;
    }

    /**
     * @param \DateTime $nextTry
     * @return Subscription
     */
    public function setNextTry(\DateTime $nextTry): Subscription
    {
        $this->nextTry = $nextTry;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     * @return Subscription
     */
    public function setStatus(?int $status): Subscription
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastRenewStatus(): string
    {
        return $this->lastRenewStatus;
    }

    /**
     * @param string $lastRenewStatus
     * @return Subscription
     */
    public function setLastRenewStatus(string $lastRenewStatus): Subscription
    {
        $this->lastRenewStatus = $lastRenewStatus;
        return $this;
    }

    /**
     * @return int
     */
    public function getAttemptsLeft(): int
    {
        return $this->attemptsLeft;
    }

    /**
     * @param int $attemptsLeft
     * @return Subscription
     */
    public function setAttemptsLeft(int $attemptsLeft): Subscription
    {
        $this->attemptsLeft = $attemptsLeft;
        return $this;
    }

    /**
     * Adds a Order
     *
     * @param \Pixelant\PxaProductManager\Domain\Model\Order $order
     * @return Subscription
     */
    public function addOrder(\Pixelant\PxaProductManager\Domain\Model\Order $order)
    {
        $this->orders->attach($order);
        return $this;
    }

    /**
     * Removes a Order
     *
     * @param \Pixelant\PxaProductManager\Domain\Model\Order $orderToRemove The Order to be removed
     * @return Subscription
     */
    public function removeOrder(\Pixelant\PxaProductManager\Domain\Model\Order $orderToRemove)
    {
        $this->orders->detach($orderToRemove);
        return $this;
    }

    /**
     * Returns the orders
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Order> $orders
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Sets the orders
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Pixelant\PxaProductManager\Domain\Model\Order> $orders
     * @return Subscription
     */
    public function setOrders(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $orders)
    {
        $this->orders = $orders;
        return $this;
    }

    /**
     * @return string
     */
    public function getSerializedProductsQuantity(): string
    {
        return $this->serializedProductsQuantity;
    }

    /**
     * @param string $serializedProductsQuantity
     * @return Subscription
     */
    public function setSerializedProductsQuantity(string $serializedProductsQuantity)
    {
        $this->serializedProductsQuantity = $serializedProductsQuantity;
        return $this;
    }

    /**
     * Save products quantity as serialized string
     *
     * @param array $productsQuantity
     * @return Subscription
     */
    public function setProductsQuantity(array $productsQuantity)
    {
        $this->setSerializedProductsQuantity(
            serialize($productsQuantity)
        );

        return $this;
    }

    /**
     * @return int
     */
    public function getSubscriptionPeriod(): int
    {
        return $this->subscriptionPeriod;
    }

    /**
     * @param int $subscriptionPeriod
     * @return Subscription
     */
    public function setSubscriptionPeriod(int $subscriptionPeriod): Subscription
    {
        $this->subscriptionPeriod = $subscriptionPeriod;
        return $this;
    }
}
