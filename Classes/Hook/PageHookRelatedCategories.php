<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook;

use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class PageHookRelatedCategories.
 */
class PageHookRelatedCategories
{
    /**
     * @param array $params
     * @param PageLayoutController $pageLayoutController
     * @return string
     */
    public function render(array $params, PageLayoutController $pageLayoutController): string
    {
        $categories = $this->findCategoriesByContentPage((int)$pageLayoutController->id);

        if (empty($categories)) {
            return '';
        }

        $categoriesData = [];
        foreach ($categories as $category) {
            $categoriesData[] = [
                'uri' => $this->editUri($category['uid']),
                'title' => $category['title'],
            ];
        }

        $view = $this->view();
        $view->assign('categories', $categoriesData);

        return $view->render();
    }

    /**
     * @return object|StandaloneView
     */
    protected function view()
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:pxa_product_manager/Resources/Private/Backend/Templates/PageModule/RelatedCategories.html'
            )
        );

        return $view;
    }

    /**
     * Edit url.
     *
     * @param int $categoryUid
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    protected function editUri(int $categoryUid): string
    {
        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        return (string)$uriBuilder->buildUriFromRoute(
            'record_edit',
            [
                "edit[sys_category][${categoryUid}]" => 'edit',
                'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI'),
            ],
            UriBuilder::ABSOLUTE_URL
        );
    }

    /**
     * Find categories uids of related page.
     *
     * @param int $page
     * @return array
     */
    protected function findCategoriesByContentPage(int $page): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category');
        $queryBuilder->getRestrictions()->removeAll()->add(
            GeneralUtility::makeInstance(DeletedRestriction::class)
        );

        return $queryBuilder
            ->select('uid', 'title')
            ->from('sys_category')
            ->where(
                $queryBuilder->expr()->eq(
                    'pxapm_content_page',
                    $queryBuilder->createNamedParameter($page, \PDO::PARAM_INT)
                )
            )
            ->execute()
            ->fetchAll();
    }
}
