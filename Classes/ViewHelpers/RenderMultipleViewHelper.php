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
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('key', 'string', 'Rendering stack key', false, null);
        $this->registerArgument('arguments', 'array', 'Arguments', false, null);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $extbaseConfiguration = $configurationManager
            ->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK);
        $config = $extbaseConfiguration['view']['renderingStacks'];
        $this->arguments['arguments']['childContent'] = $this->renderChildren();
        $childContent = $this->arguments['arguments']['childContent'];
        $output = '';
        $childContentMatch = false;

        foreach ($config as $key => $renderingStack) {
            if ($key === $this->arguments['key']) {
                foreach ($renderingStack as $renderingParts) {
                    $partOutput = htmlspecialchars_decode(
                        $this->getView($renderingParts['template'], $this->arguments)
                    );
                    $output = $output . $partOutput;

                    if (preg_match('/' . $childContent . '/', $partOutput)) {
                        $childContentMatch = true;
                    }
                }
            }
        }

        if ($childContentMatch) {
            return $output;
        }

        return $output . $childContent;
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
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $view = $objectManager->get(StandaloneView::class);
        $view->setFormat('html');
        $extbaseConfiguration = $configurationManager
            ->getConfiguration(ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRootPaths = $extbaseConfiguration['view']['templateRootPaths'];

        foreach ($templateRootPaths as $rootPath) {
            $absFilePath = GeneralUtility::getFileAbsFileName($rootPath);
            $view->setTemplatePathAndFilename($absFilePath . $path . '.html');

            if ($view->hasTemplate()) {
                $view->assignMultiple($arguments['arguments']);

                return $view->render();
            }
        }

        return '';
    }
}
