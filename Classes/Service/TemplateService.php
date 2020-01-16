<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Service;

use Pixelant\PxaProductManager\Utility\ConfigurationUtility;
use Pixelant\PxaProductManager\Utility\MainUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class TemplateService
{
    /**
     * @var StandaloneView
     */
    protected $template;

    /**
     * @var array
     */
    protected $layoutRootPaths;

    /**
     * @var array
     */
    protected $templateRootPaths;

    /**
     * @var array
     */
    protected $partialRootPaths;

    /**
     * TemplateService constructor.
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function __construct()
    {
        $this->template = MainUtility::getObjectManager()->get(StandaloneView::class);
        $viewTs = ConfigurationUtility::getTSConfig()['view'];
        $this->template->setTemplateRootPaths($viewTs['templateRootPaths']);
        $this->template->setPartialRootPaths($viewTs['partialRootPaths']);
        $this->template->setLayoutRootPaths($viewTs['layoutRootPaths']);
    }

    /**
     * @param string $templateName
     * @param array $variables
     * @return string
     */
    public function generateStandaloneTemplate(string $templateName, array $variables = [])
    {
        $this->template->setTemplate($templateName);
        $this->template->assignMultiple($variables);

        return $this->template->render();
    }

    /**
     * @param string $controller
     * @return TemplateService
     */
    public function setController(string $controller): TemplateService
    {
        $this->template->getRenderingContext()->setControllerName($controller);
        return $this;
    }

    /**
     * @param mixed $layoutRootPaths
     * @return TemplateService
     */
    public function setLayoutRootPaths(array $layoutRootPaths)
    {
        $this->template->setLayoutRootPaths($layoutRootPaths);
        return $this;
    }

    /**
     * @param array $templateRootPaths
     * @return TemplateService
     */
    public function setTemplateRootPaths(array $templateRootPaths): TemplateService
    {
        $this->template->setTemplateRootPaths($templateRootPaths);
        return $this;
    }

    /**
     * @param array $partialRootPaths
     * @return TemplateService
     */
    public function setPartialRootPaths(array $partialRootPaths): TemplateService
    {
        $this->template->setPartialRootPaths($partialRootPaths);
        return $this;
    }
}
