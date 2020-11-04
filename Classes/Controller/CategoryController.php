<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Service\NavigationService;

/**
 * Categories controller.
 */
class CategoryController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * List navigation.
     */
    public function listAction(): void
    {
        $this->view->assign('items', $this->getNavigationService()->getItems());
    }

    /**
     * Create navigation service.
     *
     * @return NavigationService
     */
    protected function getNavigationService(): NavigationService
    {
        $demand = $this->createCategoriesDemand($this->settings);

        $service = $this->objectManager->get(
            NavigationService::class,
            $this->categoryRepository->findByUid((int)$this->settings['list']['entryNavigationCategory']),
            $demand
        );
        if (!empty($this->settings['navigation']['expandAll'])) {
            $service->setExpandAll((bool)$this->settings['navigation']['expandAll']);
        }

        return $service;
    }
}
