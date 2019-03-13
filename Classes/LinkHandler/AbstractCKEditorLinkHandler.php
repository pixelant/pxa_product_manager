<?php

namespace Pixelant\PxaProductManager\LinkHandler;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Pixelant\PxaProductManager\Backend\Tree\BrowserTreeView;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\BackendWorkspaceRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Recordlist\LinkHandler\AbstractLinkHandler;
use TYPO3\CMS\Recordlist\LinkHandler\LinkHandlerInterface;
use TYPO3\CMS\Recordlist\Tree\View\LinkParameterProviderInterface;

/**
 * Link handler for page (and content) links
 */
// @codingStandardsIgnoreStart
abstract class AbstractCKEditorLinkHandler extends AbstractLinkHandler implements LinkHandlerInterface, LinkParameterProviderInterface
// @codingStandardsIgnoreEnd
{
    /**
     * Parts of the current link
     *
     * @var array
     */
    protected $linkParts = [];

    /**
     * We don't support updates since there is no difference to simply set the link again.
     *
     * @var bool
     */
    protected $updateSupported = false;

    /**
     * @var int
     */
    protected $expandPage = 0;

    /**
     * @var int
     */
    protected $pid;

    /**
     * Name of array key inside url parameters
     *
     * @var string
     */
    protected $linkPartName = '';

    /**
     * Table name
     *
     * @var string
     */
    protected $tableName = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        // remove unsupported link attributes
        foreach (['target', 'rel', 'params'] as $attribute) {
            $position = array_search($attribute, $this->linkAttributes, true);
            if ($position !== false) {
                unset($this->linkAttributes[$position]);
            }
        }

        GeneralUtility::makeInstance(PageRenderer::class)->loadRequireJsModule(
            'TYPO3/CMS/PxaProductManager/Backend/LinkHandler'
        );

        // Init variables
        $this->initLinkPartName();
        $this->initTableName();
    }

    /**
     * Checks if this is the handler for the given link
     *
     * The handler may store this information locally for later usage.
     *
     * @param array $linkParts Link parts as returned from TypoLinkCodecService
     *
     * @return bool
     */
    public function canHandleLink(array $linkParts)
    {
        if (isset($linkParts['url'][$this->linkPartName])) {
            $this->linkParts = $linkParts;

            $record = BackendUtility::getRecord(
                $this->tableName,
                (int)$this->linkParts['url'][$this->linkPartName],
                'pid'
            );

            // Save pid
            if ($record) {
                $this->pid = $record['pid'];
            }

            return true;
        }

        return false;
    }

    /**
     * Render the link handler
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function render(ServerRequestInterface $request)
    {
        $this->view->setTemplateRootPaths([
            10 => 'EXT:pxa_product_manager/Resources/Private/Backend/Templates/LinkBrowser/'
        ]);

        $this->expandPage = isset($request->getQueryParams()['expandPage'])
            ? (int)$request->getQueryParams()['expandPage']
            : 0;

        /** @var BackendUserAuthentication $backendUser */
        $backendUser = $GLOBALS['BE_USER'];

        /** @var BrowserTreeView $pageTree */
        $pageTree = GeneralUtility::makeInstance(BrowserTreeView::class);
        $pageTree->setLinkParameterProvider($this);
        $pageTree->ext_showNavTitle =
            (bool)($backendUser->getTSConfigVal('options.pageTree.showNavTitle') ?? false);
        $pageTree->ext_showPageId =
            (bool)($backendUser->getTSConfigVal('options.pageTree.showPageIdWithTitle') ?? false);
        $pageTree->ext_showPathAboveMounts =
            (bool)($backendUser->getTSConfigVal('options.pageTree.showPathAboveMounts') ?? false);
        $pageTree->addField('nav_title');

        $this->view->assignMultiple([
            'tableName' => $this->tableName,
            'tree' => $pageTree->getBrowsableTree()
        ]);

        $this->addRecordsOnExpandedPage($this->expandPage);

        return $this->view->render('ProductManager');
    }

    /**
     * @param array $values Values to be checked
     *
     * @return bool Returns TRUE if the given values match the currently selected item
     */
    public function isCurrentlySelectedItem(array $values)
    {
        return !empty($this->linkParts) && $this->pid === (int)$values['pid'];
    }

    /**
     * Returns the URL of the current script
     *
     * @return string
     */
    public function getScriptUrl()
    {
        return $this->linkBrowser->getScriptUrl();
    }

    /**
     * @param array $values Array of values to include into the parameters or which might influence the parameters
     *
     * @return string[] Array of parameters which have to be added to URLs
     */
    public function getUrlParameters(array $values)
    {
        $parameters = [
            'expandPage' => isset($values['pid']) ? (int)$values['pid'] : $this->expandPage
        ];

        return array_merge($this->linkBrowser->getUrlParameters($values), $parameters);
    }

    /**
     * @return string[] Array of body-tag attributes
     */
    public function getBodyTagAttributes()
    {
        return [];
    }

    /**
     * Format the current link for HTML output
     *
     * @return string
     */
    public function formatCurrentUrl(): string
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->tableName);

        /** @noinspection PhpParamsInspection */
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(BackendWorkspaceRestriction::class));

        $record = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($this->linkParts['url'][$this->linkPartName], \PDO::PARAM_INT)
                )
            )
            ->execute()
            ->fetch();

        if ($record) {
            $title = BackendUtility::getRecordTitle(
                $this->tableName,
                $record,
                true
            );

            return $title . ' (id = ' . $this->linkParts['url'][$this->linkPartName] . ')';
        } else {
            return '';
        }
    }

    /**
     * Assign list of records
     *
     * @param $pageId
     */
    protected function addRecordsOnExpandedPage($pageId)
    {
        // If there is an anchor value (content element reference) in the element reference, then force an ID to expand:
        if (!$pageId && isset($this->linkParts['url'][$this->linkPartName])) {
            // Set to the current link page id.
            $pageId = $this->pid;
        }
        // Draw the record list IF there is a page id to expand:
        if ($pageId
            && MathUtility::canBeInterpretedAsInteger($pageId) && $this->getBackendUser()->isInWebMount($pageId)
        ) {
            $pageId = (int)$pageId;

            $activePageRecord = BackendUtility::getRecordWSOL('pages', $pageId);
            $this->view->assign('expandActivePage', true);

            // Create header for listing, showing the page title/icon
            $this->view->assign('activePage', $activePageRecord);
            $this->view->assign('activePageTitle', BackendUtility::getRecordTitle('pages', $activePageRecord, true));
            $this->view->assign(
                'activePageIcon',
                $this->iconFactory->getIconForRecord('pages', $activePageRecord, Icon::SIZE_SMALL)->render()
            );

            // Look up tt_content elements from the expanded page
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable($this->tableName);

            /** @noinspection PhpParamsInspection */
            $queryBuilder->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
                ->add(GeneralUtility::makeInstance(BackendWorkspaceRestriction::class));

            $records = $queryBuilder
                ->select('*')
                ->from($this->tableName)
                ->where(
                    $queryBuilder->expr()->eq(
                        'pid',
                        $queryBuilder->createNamedParameter($pageId, \PDO::PARAM_INT)
                    ),
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq(
                            'sys_language_uid',
                            $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                        ),
                        $queryBuilder->expr()->eq(
                            'sys_language_uid',
                            $queryBuilder->createNamedParameter(-1, \PDO::PARAM_INT)
                        )
                    )
                )
                ->orderBy('uid', 'DESC')
                ->execute()
                ->fetchAll();

            // Enrich list of records
            foreach ($records as &$record) {
                $record['url'] = GeneralUtility::makeInstance(LinkService::class)->asString([
                    'type' => 'pxappm',
                    $this->linkPartName => $record['uid']
                ]);

                $record['isSelected'] = !empty($this->linkParts)
                    && (int)$this->linkParts['url'][$this->linkPartName] === (int)$record['uid'];
                $record['icon'] = $this->iconFactory->getIconForRecord(
                    $this->tableName,
                    $record,
                    Icon::SIZE_SMALL
                )->render();

                $record['title'] = BackendUtility::getRecordTitle(
                    $this->tableName,
                    $record,
                    true
                );
            }

            $this->view->assign('records', $records);
        }
    }

    /**
     * Init link part name
     *
     * @return void
     */
    abstract protected function initLinkPartName();

    /**
     * Init table name
     *
     * @return void
     */
    abstract protected function initTableName();
}
