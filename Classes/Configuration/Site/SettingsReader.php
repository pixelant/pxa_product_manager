<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Configuration\Site;

use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

class SettingsReader
{
    /**
     * Prefix of settings.
     *
     * @var string
     */
    protected string $prefix = 'pxapm_';

    /**
     * @var array
     */
    protected array $settings = [];

    /**
     * @var ServerRequest
     */
    protected ServerRequest $request;

    public function injectServerRequest(ServerRequest $request): void
    {
        $this->request = $request;
    }

    /**
     * Read value from settings by key.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getValue(string $key)
    {
        if (strpos($key, $this->prefix) !== 0) {
            $key = $this->prefix . $key;
        }

        return $this->settings[$key] ?? null;
    }

    /**
     * Init settings.
     */
    protected function init(): void
    {
        $site = $this->request->getAttribute('site');
        if ($site && ($site instanceof Site)) {
            $this->settings = $site->getConfiguration();
        }
    }
}
