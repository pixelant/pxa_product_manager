<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use Pixelant\PxaProductManager\Domain\Model\Order;
use Pixelant\PxaProductManager\Domain\Model\Subscription;
use Pixelant\PxaProductManager\Domain\Repository\SubscriptionRepository;
use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class SubscriptionService
{
    /**
     * @var SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * @var int
     * TODO: get from settings
     */
    protected $numberOfAttempts = 3;

    /**
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function injectSubscriptionRepository(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @return Subscription
     */
    protected function createEmptySubscription()
    {
        return MainUtility::getObjectManager()->get(Subscription::class);
    }

    /**
     * @param Order $order
     * @param bool $persist
     * @return Subscription
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function createFromOrder(Order $order, bool $persist = false)
    {
        $subscription = ($this->createEmptySubscription())
            ->setRenewDate(new \DateTime())
            ->setNextTry(new \DateTime())
            ->setStatus(Subscription::STATUS_ACTIVE)
            ->setAttemptsLeft($this->numberOfAttempts)
            ->setSerializedProductsQuantity($order->getSerializedProductsQuantity())
            ->addOrder($order)
            ->setLastRenewStatus(Subscription::RENEW_STATUS_SUCCESS);

        $subscription->setPid($order->getPid());

        $this->subscriptionRepository->add($subscription);

        if ($persist) {
            GeneralUtility::makeInstance(PersistenceManager::class)->persistAll();
        }

        return $subscription;
    }

    /**
     * @param Subscription $subscription
     * @return Subscription
     */
    public function updateRenewalDates(Subscription $subscription)
    {
        $renewalDate = $subscription->getRenewDate();
        switch ($subscription->getSubscriptionPeriod()) {
            case Subscription::RECURRING_FOR_WEEK:
                $timeModifier = Subscription::WEEK_TIME_MODIFIER;
                break;
            case Subscription::RECURRING_FOR_MONTH:
            default:
                $timeModifier = Subscription::MONTH_TIME_MODIFIER;
                break;
        }

        $renewalDate->modify($timeModifier);
        $subscription->setRenewDate($renewalDate);
        $subscription->setNextTry($renewalDate);
        return $subscription;
    }

    /**
     * @param Subscription $subscription
     * @return Subscription
     */
    public function prepareForNextRenew(Subscription $subscription)
    {
        $subscription = $this->updateRenewalDates($subscription);
        $subscription->setAttemptsLeft($this->numberOfAttempts);

        return $subscription;
    }
}
