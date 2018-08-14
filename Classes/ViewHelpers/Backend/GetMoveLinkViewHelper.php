<?php

namespace Pixelant\PxaProductManager\ViewHelpers\Backend;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Generate sorting link
 *
 * @package Pixelant\PxaProductManager\ViewHelpers\Backend
 */
class GetMoveLinkViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('uid', 'int', 'Record uid', true);
        $this->registerArgument('table', 'string', 'Table name', true);
        $this->registerArgument('positionUid', 'int', 'Uid of record to move to', true);
    }

    /**
     * Create sorting link
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
        $uid = (int)$arguments['uid'];
        $positionUid = (int)$arguments['positionUid'];
        $table = trim($arguments['table']);

        if ($uid && $table && $positionUid) {
            $params = '&cmd[' . $table . '][' . $uid . '][move]=' . $positionUid;

            return BackendUtility::getLinkToDataHandlerAction($params);
        }

        return '';
    }
}
