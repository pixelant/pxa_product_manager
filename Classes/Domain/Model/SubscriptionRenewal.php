<?php

namespace Pixelant\PxaProductManager\Domain\Model;

/**
 * Class SubscriptionRenewal
 * @package Pixelant\PxaProductManager\Domain\Model
 */
class SubscriptionRenewal extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * $paymentDate
     *
     * @var \DateTime
     */
    protected $paymentDate = null;

    /**
     * $paymentNextTry
     *
     * @var \DateTime
     */
    protected $paymentNextTry = null;

    /**
     * $paymentDone
     *
     * @var bool
     */
    protected $paymentDone = null;

    /**
     * $paymentAttemptsLeft
     *
     * @var int
     */
    protected $paymentAttemptsLeft = null;

    /**
     * @var string
     */
    protected $paymentId;

    /**
     * @var string
     */
    protected $paymentStatus;

    /**
     * $shipmentDate
     *
     * @var \DateTime
     */
    protected $shipmentDate = null;

    /**
     * $shipmentNextTry
     *
     * @var \DateTime
     */
    protected $shipmentNextTry = null;

    /**
     * $shipmentDone
     *
     * @var bool
     */
    protected $shipmentDone = null;

    /**
     * $shipmentAttemptsLeft
     *
     * @var int
     */
    protected $shipmentAttemptsLeft = null;

    /**
     * @return \DateTime
     */
    public function getPaymentDate(): \DateTime
    {
        return $this->paymentDate;
    }

    /**
     * @param \DateTime $paymentDate
     * @return SubscriptionRenewal
     */
    public function setPaymentDate(\DateTime $paymentDate): SubscriptionRenewal
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPaymentNextTry(): \DateTime
    {
        return $this->paymentNextTry;
    }

    /**
     * @param \DateTime $paymentNextTry
     * @return SubscriptionRenewal
     */
    public function setPaymentNextTry(\DateTime $paymentNextTry): SubscriptionRenewal
    {
        $this->paymentNextTry = $paymentNextTry;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPaymentDone(): bool
    {
        return $this->paymentDone;
    }

    /**
     * @param bool $paymentDone
     * @return SubscriptionRenewal
     */
    public function setPaymentDone(bool $paymentDone): SubscriptionRenewal
    {
        $this->paymentDone = $paymentDone;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getShipmentDate(): \DateTime
    {
        return $this->shipmentDate;
    }

    /**
     * @param \DateTime $shipmentDate
     * @return SubscriptionRenewal
     */
    public function setShipmentDate(\DateTime $shipmentDate): SubscriptionRenewal
    {
        $this->shipmentDate = $shipmentDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getShipmentNextTry(): \DateTime
    {
        return $this->shipmentNextTry;
    }

    /**
     * @param \DateTime $shipmentNextTry
     * @return SubscriptionRenewal
     */
    public function setShipmentNextTry(\DateTime $shipmentNextTry): SubscriptionRenewal
    {
        $this->shipmentNextTry = $shipmentNextTry;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShipmentDone(): bool
    {
        return $this->shipmentDone;
    }

    /**
     * @param bool $shipmentDone
     * @return SubscriptionRenewal
     */
    public function setShipmentDone(bool $shipmentDone): SubscriptionRenewal
    {
        $this->shipmentDone = $shipmentDone;
        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentAttemptsLeft(): int
    {
        return $this->paymentAttemptsLeft;
    }

    /**
     * @param int $paymentAttemptsLeft
     * @return SubscriptionRenewal
     */
    public function setPaymentAttemptsLeft(int $paymentAttemptsLeft): SubscriptionRenewal
    {
        $this->paymentAttemptsLeft = $paymentAttemptsLeft;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * @param string $paymentId
     * @return SubscriptionRenewal
     */
    public function setPaymentId(string $paymentId): SubscriptionRenewal
    {
        $this->paymentId = $paymentId;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    /**
     * @param string $paymentStatus
     * @return SubscriptionRenewal
     */
    public function setPaymentStatus(string $paymentStatus): SubscriptionRenewal
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    /**
     * @return int
     */
    public function getShipmentAttemptsLeft(): int
    {
        return $this->shipmentAttemptsLeft;
    }

    /**
     * @param int $shipmentAttemptsLeft
     * @return SubscriptionRenewal
     */
    public function setShipmentAttemptsLeft(int $shipmentAttemptsLeft): SubscriptionRenewal
    {
        $this->shipmentAttemptsLeft = $shipmentAttemptsLeft;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasMorePaymentAttempts()
    {
        return $this->paymentAttemptsLeft > 0;
    }

    /**
     * @return bool
     */
    public function hasMoreShipmentAttempts()
    {
        return $this->shipmentAttemptsLeft > 0;
    }

    /**
     * @return int
     */
    public function decrementPaymentAttempt()
    {
        $attemptsLeft = $this->getPaymentAttemptsLeft();
        $attemptsLeft = ($attemptsLeft <= 0) ? 0 : $attemptsLeft - 1;
        $this->setPaymentAttemptsLeft($attemptsLeft);
        return $attemptsLeft;
    }

    /**
     * @return \DateTime
     */
    public function makeNextPaymentTryTomorrow()
    {
        $nextTry = $this->getPaymentNextTry();
        $nextTry->modify('+1 day');
        $this->setPaymentNextTry($nextTry);
        return $nextTry;
    }
}
