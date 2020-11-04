<?php

namespace Pixelant\PxaProductManager\LinkHandler;

/*
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
 */

use Pixelant\PxaProductManager\Backend\Tree\BrowserTreeView;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\RecordList\ElementBrowserRecordList;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\BackendWorkspaceRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Recordlist\LinkHandler\AbstractLinkHandler as Typo3LinkHandler;
use TYPO3\CMS\Recordlist\LinkHandler\LinkHandlerInterface;
use TYPO3\CMS\Recordlist\Tree\View\LinkParameterProviderInterface;

/**
 * Link handler for page (and content) links.
 */
abstract class AbstractLinkHandler extends Typo3LinkHandler implements LinkHandlerInterface, LinkParameterProviderInterface
{
    /**
     * Parts of the current link.
     *
     * @var array
     */
    protected array $linkParts = [];

    /**
     * We don't support updates since there is no difference to simply set the link again.
     *
     * @var bool
     */
    protected $updateSupported = false;

    /**
     * @var int
     */
    protected int $expandPage = 0;

    /**
     * @var int
     */
    protected int $pid = 0;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        // Remove unsupported link attributes
        $this->linkAttributes = array_filter($this->linkAttributes, function ($attribute) {
            return !in_array($attribute, ['target', 'rel', 'params'], true);
        });

        GeneralUtility::makeInstance(PageRenderer::class)->loadRequireJsModule(
            'TYPO3/CMS/PxaProductManager/Backend/LinkHandler'
        );
    }

    /**
     * Checks if this is the handler for the given link.
     *
     * The handler may store this information locally for later usage.
     *
     * @param array $linkParts Link parts as returned from TypoLinkCodecService
     *
     * @return bool
     */
    public function canHandleLink(array $linkParts): bool
    {
        if (isset($linkParts['url'][$this->linkName()])) {
            $this->linkParts = $linkParts;

            $record = BackendUtility::getRecord(
                $this->tableName(),
                (int)$this->linkParts['url'][$this->linkName()],
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
     * Render the link handler.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function render(ServerRequestInterface $request): string
    {
        $this->view->setTemplateRootPaths([
            10 => 'EXT:pxa_product_manager/Resources/Private/Backend/Templates/LinkBrowser/',
        ]);

        $this->expandPage = (int) ($request->getQueryParams()['expandPage'] ?? 0);

        $tsConfig = $GLOBALS['BE_USER']->getTSConfig();

        /** @var BrowserTreeView $pageTree */
        $pageTree = GeneralUtility::makeInstance(BrowserTreeView::class);
        $pageTree->setLinkParameterProvider($this);
        $pageTree->ext_showNavTitle = (bool)($tsConfig['options.']['pageTree.']['showNavTitle'] ?? false);
        $pageTree->ext_showPageId = (bool)($tsConfig['options.']['pageTree.']['showPageIdWithTitle'] ?? false);
        $pageTree->ext_showPathAboveMounts = (bool)($tsConfig['options.']['pageTree.']['showPathAboveMounts'] ?? false);
        $pageTree->addField('nav_title');

        $this->view->assignMultiple([
            'tableName' => $this->tableName(),
            'tree' => $pageTree->getBrowsableTree(),
        ]);

        $this->addRecordsOnExpandedPage($this->expandPage);

        return $this->view->render('ProductManager');
    }

    /**
     * @param array $values Values to be checked
     *
     * @return bool Returns TRUE if the given values match the currently selected item
     */
    public function isCurrentlySelectedItem(array $values): bool
    {
        $compareToPid = $this->expandPage ?: $this->pid;

        return $compareToPid === (int)$values['pid'];
    }

    /**
     * Returns the URL of the current script.
     *
     * @return string
     */
    public function getScriptUrl(): string
    {
        return $this->linkBrowser->getScriptUrl();
    }

    /**
     * @param array $values Array of values to include into the parameters or which might influence the parameters
     *
     * @return string[] Array of parameters which have to be added to URLs
     */
    public function getUrlParameters(array $values): array
    {
        $parameters = [
            'expandPage' => isset($values['pid']) ? (int)$values['pid'] : $this->expandPage,
        ];

        return array_merge($this->linkBrowser->getUrlParameters($values), $parameters);
    }

    /**
     * @return string[] Array of body-tag attributes
     */
    public function getBodyTagAttributes()
    {
        return [
            'data-typolink-template' => GeneralUtility::makeInstance(LinkService::class)->asString([
                'type' => 'pxappm',
                $this->linkName() => '###RECORD_UID###',
            ]),
        ];
    }

    /**
     * Format the current link for HTML output.
     *
     * @return string
     */
    public function formatCurrentUrl(): string
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($this->tableName());

        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(BackendWorkspaceRestriction::class));

        $record = $queryBuilder
            ->select('*')
            ->from($this->tableName())
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($this->linkParts['url'][$this->linkName()], \PDO::PARAM_INT)
                )
            )
            ->execute()
            ->fetch();

        if ($record) {
            $title = BackendUtility::getRecordTitle(
                $this->tableName(),
                $record,
                true
            );

            return $title . ' (id = ' . $this->linkParts['url'][$this->linkName()] . ')';
        }

        return '';
    }

    /**
     * Assign list of records.
     *
     * @param $pageId
     */
    protected function addRecordsOnExpandedPage($pageId): void
    {
        // If there is an anchor value (content element reference) in the element reference, then force an ID to expand:
        if (!$pageId && isset($this->linkParts['url'][$this->linkName()])) {
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

            $permsClause = $this->getBackendUser()->getPagePermsClause(Permission::PAGE_SHOW);
            $pageInfo = BackendUtility::readPageAccess($pageId, $permsClause);

            /** @var ElementBrowserRecordList $dbList */
            $dbList = GeneralUtility::makeInstance(ElementBrowserRecordList::class);
            $dbList->setOverrideUrlParameters($this->getUrlParameters([]));
            $dbList->thisScript = $this->getScriptUrl();
            $dbList->thumbs = false;
            $dbList->setIsEditable(false);
            $dbList->calcPerms = $this->getBackendUser()->calcPerms($pageInfo);
            $dbList->noControlPanels = true;
            $dbList->clickMenuEnabled = false;
            $dbList->tableList = $this->tableName();
            $dbList->hideTranslations = $this->tableName();

            $dbList->start(
                $pageId,
                GeneralUtility::_GP('table'),
                MathUtility::forceIntegerInRange(GeneralUtility::_GP('pointer'), 0, 100000),
                GeneralUtility::_GP('search_field'),
                GeneralUtility::_GP('search_levels'),
                GeneralUtility::_GP('showLimit')
            );

            $dbList->setDispFields();
            $dbList->generateList();

            $dbListHTML = $dbList->getSearchBox();
            $dbListHTML .= $dbList->HTMLcode;

            $this->view->assign('dbListHTML', $dbListHTML);
        }
    }

    /**
     * Prefix of link handler.
     *
     * @return string
     */
    abstract protected function linkName(): string;

    /**
     * Table name of links records.
     *
     * @return string
     */
    abstract protected function tableName(): string;
}
