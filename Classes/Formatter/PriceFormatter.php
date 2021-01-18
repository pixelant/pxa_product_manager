<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Formatter;

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Event\Product\FormatPriceEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class PriceFormatter implements SingletonInterface
{
    /**
     * @var string
     */
    protected string $locale = 'en_US';

    /**
     * @var string
     */
    protected string $currency = 'USD';

    /**
     * @var int
     */
    protected int $fractionDigits = 2;

    /**
     * @var ConfigurationManagerInterface
     */
    protected ConfigurationManagerInterface $configurationManager;

    /**
     * @var ServerRequest
     */
    protected ServerRequest $request;

    /**
     * @var EventDispatcherInterface
     */
    protected EventDispatcherInterface $eventDispatcher;

    /**
     * @param ServerRequest $request
     */
    public function __construct(ServerRequest $request = null)
    {
        $this->request = $request ?? $GLOBALS['TYPO3_REQUEST'];
    }

    /**
     * @param ConfigurationManagerInterface $configurationManagerInterface
     */
    public function injectConfigurationManagerInterface(
        ConfigurationManagerInterface $configurationManagerInterface
    ): void {
        $this->configurationManager = $configurationManagerInterface;
    }

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function injectDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher ?? GeneralUtility::makeInstance(EventDispatcher::class);
    }

    /**
     * On init set currency and locale.
     */
    public function initializeObject(): void
    {
        $settings = $this->readSettings();

        if (!empty($settings['price']['currency'])) {
            $this->setCurrency($settings['price']['currency']);
        }

        if (is_numeric($settings['price']['fractionDigits'])) {
            $this->setFractionDigits((int)$settings['price']['fractionDigits']);
        }
    }

    /**
     * Format product price according to locale and currency.
     *
     * @param Product $product
     * @param string|null $locale
     * @param string|null $currency
     * @param int|null    $fractionDigits
     * @return string
     */
    public function format(
        Product $product,
        string $locale = null,
        string $currency = null,
        int $fractionDigits = null
    ): string {
        $locale ??= $this->getLocale();
        $currency ??= $this->getCurrency();
        $fractionDigits ??= $this->getFractionDigits();

        $event = new FormatPriceEvent($currency, $locale, $fractionDigits);
        $this->eventDispatcher->dispatch($event);
        $formatter = $event->getFormatter();

        return $formatter->formatCurrency($product->getPrice(), $event->getCurrency());
    }

    /**
     * Read settings from typoscript.
     *
     * @return array
     */
    protected function readSettings(): array
    {
        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'PxaProductManager',
            'Pi1'
        );

        return $settings ?? [];
    }

    /**
     * Set locale from site settings.
     */
    protected function setLocaleFromRequest(): void
    {
        $siteLanguage = $this->request->getAttribute('language', null);
        if ($siteLanguage instanceof SiteLanguage) {
            [$this->locale] = explode('.', $siteLanguage->getLocale());
        }
    }

    /**
     * Get the value of locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the value of locale.
     *
     * @param string  $locale
     *
     * @return PriceFormatter
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get the value of currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set the value of currency.
     *
     * @param string  $currency
     *
     * @return PriceFormatter
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get the value of fractionDigits.
     *
     * @return int
     */
    public function getFractionDigits()
    {
        return $this->fractionDigits;
    }

    /**
     * Set the value of fractionDigits.
     *
     * @param int  $fractionDigits
     *
     * @return PriceFormatter
     */
    public function setFractionDigits(int $fractionDigits): self
    {
        $this->fractionDigits = $fractionDigits;

        return $this;
    }
}
