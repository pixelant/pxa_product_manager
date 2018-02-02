<?php

namespace Pixelant\PxaProductManager\ViewHelpers\Backend;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Get edit url for records
 *
 * @package Pixelant\PxaProductManager\ViewHelpers\Backend
 */
class RecordEditUrlViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     *
     * @api
     */
    public function initializeArguments()
    {
        $this->registerArgument('uid', 'int', 'Record uid', true);
        $this->registerArgument('table', 'string', 'Table name', false, 'sys_category');
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $table = $arguments['table'];
        $uid = $arguments['uid'];

        $url = BackendUtility::getModuleUrl('record_edit', [
            'edit[' . $table . '][' . $uid . ']' => 'edit',
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI')
        ]);

        return $url;
    }
}
