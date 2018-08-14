<?php

namespace Pixelant\PxaProductManager\ViewHelpers\Backend;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Generate icon for record
 *
 * @package Pixelant\PxaProductManager\ViewHelpers\Backend
 */
class GetRecordIconViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @var IconFactory
     */
    private static $iconFactory = null;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('uid', 'int', 'Record uid', true);
        $this->registerArgument('table', 'string', 'Table name', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $uid = (int)$arguments['uid'];
        $table = trim($arguments['table']);

        if ($uid && $table) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
            $queryBuilder
                ->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

            $row = $queryBuilder
                ->select('*')
                ->from($table)
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)
                    )
                )
                ->execute()
                ->fetch();

            if (is_array($row)) {
                return self::getIconFactory()->getIconForRecord($table, $row, Icon::SIZE_SMALL)->render();
            }
        }

        return '';
    }

    /**
     * Wrapper for icon factory
     *
     * @return object|IconFactory
     */
    private static function getIconFactory()
    {
        if (self::$iconFactory === null) {
            self::$iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        }

        return self::$iconFactory;
    }
}
