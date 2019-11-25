<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use Pixelant\PxaProductManager\Domain\Model\Order;
use Pixelant\PxaProductManager\Domain\Model\SubscriptionRenewal;
use Pixelant\PxaProductManager\Domain\Repository\OrderRepository;
use Pixelant\PxaProductManager\Exception\NotARecurringOrderException;
use Pixelant\PxaProductManager\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class SubscriptionRenewalService
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var int
     */
    protected $maxPaymentAttempts = 3;

    /**
     * @var int
     */
    protected $maxShipmentAttempts = 3;

    /**
     * @var \DateTime
     */
    protected $today;

    /**
     * SubscriptionRenewalService constructor.
     * @param Order $order
     * @throws NotARecurringOrderException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function __construct(Order $order)
    {
        $settings = ConfigurationUtility::getSettings();

        $this->today = new \DateTime();

        if ($settings['payments']['debug'] === '1' && $settings['payments']['todayDate']) {
            try {
                $this->today = \DateTime::createFromFormat('Y-m-d', $settings['payments']['todayDate']);
            } catch (\Exception $e) {
                // Do nothing
            }
        }

        $this->order = $order;
        if ($this->order->getRecurringPeriod() <= 0) {
            throw new NotARecurringOrderException();
        }
    }

    /**
     * @return SubscriptionRenewal|null
     */
    public function getNextRenewal()
    {
        $renewal = $this->getLatestRenewal();

        if (!$renewal) {
            $renewal = $this->createNextRenewal($renewal);
        }

        return $renewal;
    }

    /**
     * @return SubscriptionRenewal|null
     */
    protected function getLatestRenewal()
    {
        return array_pop($this->order->getRenewals()->toArray());
    }

    /**
     * @param \DateTime $date
     * @return SubscriptionRenewal
     */
    public function addRenewal(\DateTime $date)
    {
        $newRenewal = $this->createRenewal($date);

        $this->order->addRenewal($newRenewal);
        GeneralUtility::makeInstance(ObjectManager::class)->get(OrderRepository::class)->update($this->order);
        GeneralUtility::makeInstance(PersistenceManager::class)->persistAll();
        return $newRenewal;
    }

    /**
     * @param SubscriptionRenewal|null $latestRenewal
     * @return SubscriptionRenewal
     */
    public function createNextRenewal(SubscriptionRenewal $latestRenewal = null)
    {
        if (!$latestRenewal) {
            $latestRenewal = $this->getLatestRenewal();
        }

        // If no renewals exist yet
        if (!$latestRenewal) {
            return $this->addRenewal($this->order->getCrdate());
        } else {
            $date = $this->getNextRenewalDate($latestRenewal);
            return $this->addRenewal($date);
        }
    }

    /**
     * @param SubscriptionRenewal $lastRenewal
     * @return \DateTime
     */
    public function getNextRenewalDate(SubscriptionRenewal $lastRenewal)
    {
        $nextRenewalDate = clone($lastRenewal->getPaymentDate());

        switch ($this->order->getRecurringPeriod()) {
            case Order::RECURRING_FOR_WEEK:
                $timeModifier = Order::WEEK_TIME_MODIFIER;
                break;
            case Order::RECURRING_FOR_MONTH:
            default:
                $timeModifier = Order::MONTH_TIME_MODIFIER;
                break;
        }

        return $nextRenewalDate->modify($timeModifier);
    }

    /**
     * @param \DateTime $date
     * @return SubscriptionRenewal
     */
    protected function createRenewal(\DateTime $date)
    {
        $renewal = (new SubscriptionRenewal())
            ->setPaymentDate($date)
            ->setPaymentNextTry($date)
            ->setPaymentDone(false)
            ->setPaymentAttemptsLeft($this->maxPaymentAttempts)
            ->setShipmentDate($date)
            ->setShipmentNextTry($date)
            ->setShipmentDone(false)
            ->setShipmentAttemptsLeft($this->maxShipmentAttempts)
        ;

        $renewal->setPid($this->order->getPid());
        return $renewal;
    }

    /**
     * @param SubscriptionRenewal $renewal
     * @return bool
     */
    public function isOngoingPayment(SubscriptionRenewal $renewal)
    {
        return !$renewal->isPaymentDone() && $renewal->getPaymentAttemptsLeft() > 0;
    }

    /**
     * @param SubscriptionRenewal $renewal
     * @return bool
     * @throws \Exception
     */
    public function isItTimeToMakePayment(SubscriptionRenewal $renewal)
    {
        return $this->today > $renewal->getPaymentNextTry();
    }
}
