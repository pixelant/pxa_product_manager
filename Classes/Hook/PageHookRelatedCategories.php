<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook;

use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Service\TemplateService;
use Pixelant\PxaProductManager\Utility\ConfigurationUtility;
use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class PageHookRelatedCategories
 * @package Pixelant\PxaProductManager\Hook
 */
class PageHookRelatedCategories
{
    /**
     * @param array $params
     * @param PageLayoutController $pageLayoutController
     * @return string
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function render(array $params, PageLayoutController $pageLayoutController)
    {
        /** @var CategoryRepository $categoriesRepository */
        $categoriesRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(CategoryRepository::class);
        $categories = $categoriesRepository->findByRelatedToContentPage($pageLayoutController->id);

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $data = [];
        foreach ($categories as $category) {
            $params = [
                "edit[sys_category][{$category['uid']}]" => 'edit',
                'returnUrl' => \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('REQUEST_URI')
            ];

            try {
                $uri = $uriBuilder->buildUriFromRoute('record_edit', $params, UriBuilder::ABSOLUTE_URL);
            } catch (\TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException $e) {
                return 'Can\'t generate the link';
            }

            $uri = (string)$uri;

            $data[] = [
                'uri' => $uri,
                'title' => $category['title']
            ];
        }

        $backendPathsTs = ConfigurationUtility::getTSConfig()['backend'];

        /** @var TemplateService $templateService */
        $templateService = GeneralUtility::makeInstance(TemplateService::class);

        $templateService->setTemplateRootPaths($backendPathsTs['templateRootPaths'])
                        ->setPartialRootPaths($backendPathsTs['partialRootPaths'])
                        ->setLayoutRootPaths($backendPathsTs['layoutRootPaths']);

        return $templateService->generateStandaloneTemplate('PageModule/RelatedCategories', [
            'data' => $data
        ]);
    }
}
