<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class RenderMultipleViewHelper extends AbstractViewHelper
{
    /**
     * @var array
     */
    protected $extbaseConfiguration = [];

    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arbuments.
     *
     * @return void
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('key', 'string', 'Rendering stack key', false, null);
        $this->registerArgument('arguments', 'array', 'Arguments', false, null);
    }

    /**
     * Initialize viewhelper.
     *
     * @return void
     */
    public function initialize(): void
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $this->extbaseConfiguration = $configurationManager
            ->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK);
    }

    /**
     * Render viewhelper.
     *
     * @return null|string
     */
    public function render(): ?string
    {
        $config = $this->extbaseConfiguration['view']['renderingStacks'];
        $renderingStack = $config[$this->arguments['key']] ?? false;

        if (empty($renderingStack)) {
            return null;
        }

        $parts = 0;
        $output = '';
        $childContent = false;

        krsort($renderingStack, SORT_NUMERIC);

        foreach ($renderingStack as $renderingParts) {
            if ($parts === 0) {
                $this->arguments['arguments']['childContent'] = $this->renderChildren();
            } else {
                $this->arguments['arguments']['childContent'] = $childContent[$parts - 1] ?? '';
            }

            $childContent[$parts] = htmlspecialchars_decode(
                $this->getView($renderingParts['template'], $this->arguments)
            );
            $parts++;
        }
        if (is_array($childContent)) {
            $output = $childContent[count($childContent) - 1];
        }

        return $output;
    }

    /**
     * Create view from demanded template and arguments.
     *
     * @param string $path
     * @param array $arguments
     * @return string
     */
    public function getView(string $path, array $arguments): string
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $view = $objectManager->get(StandaloneView::class);
        $view->setFormat('html');
        $view->setTemplateRootPaths($this->extbaseConfiguration['view']['templateRootPaths']);
        $view->setPartialRootPaths($this->extbaseConfiguration['view']['partialRootPaths']);
        $view->setLayoutRootPaths($this->extbaseConfiguration['view']['layoutRootPaths']);
        $view->setTemplate($path);
        $view->assignMultiple($arguments['arguments']);

        return $view->render();
    }
}
