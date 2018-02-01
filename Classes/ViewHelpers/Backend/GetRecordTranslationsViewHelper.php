<?php

namespace Pixelant\PxaProductManager\ViewHelpers\Backend;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Generate icon for record
 *
 * @package Pixelant\PxaProductManager\ViewHelpers\Backend
 */
class GetRecordTranslationsViewHelper extends AbstractViewHelper
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
            $transOrigPointerField = $GLOBALS['TCA'][$table]['ctrl']['transOrigPointerField'] ?: 'l10n_parent';
            $languageField = $GLOBALS['TCA'][$table]['ctrl']['languageField'] ?: 'sys_language_uid';

            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
            $queryBuilder
                ->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

            $rows = $queryBuilder
                ->select('*')
                ->from($table)
                ->where(
                    $queryBuilder->expr()->eq(
                        $transOrigPointerField,
                        $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)
                    ),
                    $queryBuilder->expr()->gt(
                        $languageField,
                        $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                    )
                )
                ->execute()
                ->fetchAll();

            if (is_array($rows)) {
                return $rows;
            }
        }

        return [];
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
