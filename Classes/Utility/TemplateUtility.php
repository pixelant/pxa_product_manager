<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Utility;

use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Fluid\View\StandaloneView;

class TemplateUtility
{
    /**
     * @param string $templateName
     * @param array $variables
     * @return mixed
     * @throws InvalidConfigurationTypeException
     */
    public static function generateStandaloneTemplate(string $templateName, array $variables = [])
    {
        /** @var StandaloneView $view */
        $template = MainUtility::getObjectManager()->get(StandaloneView::class);

        $viewTs = ConfigurationUtility::getTSConfig()['view'];

        $template->setTemplate($templateName);
        $template->setTemplateRootPaths($viewTs['templateRootPaths']);
        $template->setPartialRootPaths($viewTs['partialRootPaths']);
        $template->setLayoutRootPaths($viewTs['layoutRootPaths']);
        $template->assignMultiple($variables);
        $content = $template->render();

        return $content;
    }
}
