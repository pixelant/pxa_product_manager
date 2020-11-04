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

    /**
     * @param ServerRequest $request
     */
    public function __construct(ServerRequest $request = null)
    {
        $this->request = $request ?? $GLOBALS['TYPO3_REQUEST'];
        $this->init();
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
        if (($site = $this->request->getAttribute('site')) && ($site instanceof Site)) {
            $this->settings = $site->getConfiguration();
        }
    }
}
