<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\ViewHelpers\Widget;

use Pixelant\PxaProductManager\ViewHelpers\Widget\Controller\PaginateController;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;

/**
 * Class PaginateViewHelper
 * @package Pixelant\PxaProductManager\ViewHelpers\Widget
 */
class PaginateViewHelper extends AbstractWidgetViewHelper
{
    /**
     * @var PaginateController
     */
    protected $controller;

    /**
     * @param PaginateController $controller
     */
    public function injectPaginateController(
        PaginateController $controller
    ) {
        $this->controller = $controller;
    }

    /**
     * Initialize arguments.
     *
     * @api
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('objects', 'mixed', 'Object', true);
        $this->registerArgument('as', 'string', 'as', true);
        $this->registerArgument(
            'configuration',
            'array',
            'configuration',
            false,
            ['itemsPerPage' => 10, 'insertAbove' => false, 'insertBelow' => true, 'maximumNumberOfLinks' => 99]
        );
    }

    /**
     * @return string
     * @throws \UnexpectedValueException
     */
    public function render()
    {
        $objects = $this->arguments['objects'];

        if (!($objects instanceof QueryResultInterface || $objects instanceof ObjectStorage || is_array($objects))) {
            // @codingStandardsIgnoreStart
            throw new \UnexpectedValueException('Supplied file object type ' . get_class($objects) . ' must be QueryResultInterface or ObjectStorage or be an array.', 1454510731);
            // @codingStandardsIgnoreEnd
        }
        return $this->initiateSubRequest();
    }
}
