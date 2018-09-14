<?php

namespace Pixelant\PxaProductManager\ViewHelpers\Backend;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Get record from DB
 *
 * @package Pixelant\PxaProductManager\ViewHelpers\Backend
 */
class GetDbRecordViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeChildren = false;

    protected $escapeOutput = false;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('uid', 'int', 'Record uid', true);
        $this->registerArgument('table', 'string', 'Table name', true);
        $this->registerArgument('as', 'string', 'Render as variable', false, '');
    }

    /**
     * Get record from db
     *
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
        $as = trim($arguments['as']);

        if ($uid && $table) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
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

            if (!empty($as)) {
                $variableProvider = $renderingContext->getVariableProvider();
                if ($variableProvider->exists($as)) {
                    $variableProvider->remove($as);
                }

                $variableProvider->add($as, $row);
                $content = $renderChildrenClosure();
                $variableProvider->remove($as);

                return $content;
            }

            return $row;
        }

        return null;
    }
}
