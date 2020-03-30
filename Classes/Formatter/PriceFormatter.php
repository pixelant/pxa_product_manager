<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Formatter;

use NumberFormatter;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * @package Pixelant\PxaProductManager\Formatter
 */
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
     * @var ConfigurationManagerInterface
     */
    protected ConfigurationManagerInterface $configurationManager;

    /**
     * @var ServerRequest
     */
    protected ServerRequest $request;

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
    public function injectConfigurationManagerInterface(ConfigurationManagerInterface $configurationManagerInterface)
    {
        $this->configurationManager = $configurationManagerInterface;
    }

    /**
     * On init set currency and locale
     */
    public function initializeObject()
    {
        $this->setCurrencyFromSettings();
        $this->setCurrencyFromRequest();
    }

    /**
     * Format price according to locale and currency
     *
     * @param float $price
     *
     * @param string|null $locale
     * @param string|null $currency
     * @return string
     */
    public function format(float $price, string $locale = null, string $currency = null): string
    {
        $locale ??= $this->locale;
        $currency ??= $this->currency;

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($price, $currency);
    }

    /**
     * Set currency from plugin settings
     */
    protected function setCurrencyFromSettings(): void
    {
        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'PxaProductManager',
            'Pi1'
        );

        if (! empty($settings['price']['currency'])) {
            $this->currency = $settings['price']['currency'];
        }
    }

    /**
     * Set locale from site settings
     */
    protected function setCurrencyFromRequest(): void
    {
        $siteLanguage = $this->request->getAttribute('language', null);
        if ($siteLanguage instanceof SiteLanguage) {
            list($this->locale) = explode('.', $siteLanguage->getLocale());
        }
    }
}
