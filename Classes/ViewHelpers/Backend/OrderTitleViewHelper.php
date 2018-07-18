<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers\Backend;

use Pixelant\PxaProductManager\Domain\Model\Order;
use TYPO3\CMS\Core\Service\MarkerBasedTemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Class OrderTitleViewHelper
 * @package Pixelant\PxaProductManager\ViewHelpers\Backend
 */
class OrderTitleViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize
     */
    public function initializeArguments()
    {
        $this->registerArgument('order', Order::class, 'Order mode', false, null);
        $this->registerArgument('titleTemplate', 'string', 'Title template with markers', true);
    }

    /**
     * Generate title string from markers
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $order = $arguments['order'] ?? $renderChildrenClosure();
        if (!is_object($order) || !$order instanceof Order) {
            return '';
        }
        $titleTemplate = $arguments['titleTemplate'];
        $orderFields = [];

        foreach ($order->getOrderFields() as $field => $fieldConfiguration) {
            $orderFields['###' . $field . '###'] = $fieldConfiguration['value'];
        }

        $markerBasedTemplateService = self::getMarkerBasedTemplateService();

        return $markerBasedTemplateService->substituteMarkerArray($titleTemplate, $orderFields, '', true);
    }

    /**
     * @return MarkerBasedTemplateService
     */
    protected static function getMarkerBasedTemplateService(): MarkerBasedTemplateService
    {
        static $markerBasedTemplateService;
        if ($markerBasedTemplateService === null) {
            $markerBasedTemplateService = GeneralUtility::makeInstance(MarkerBasedTemplateService::class);
        }

        return $markerBasedTemplateService;
    }
}
