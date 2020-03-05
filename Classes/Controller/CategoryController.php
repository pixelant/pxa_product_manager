<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\CategoryDemand;
use Pixelant\PxaProductManager\Domain\Model\DTO\Factory\CategoryDemandFactory;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Service\NavigationService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Categories controller
 */
class CategoryController extends ActionController
{
    /**
     * @var NavigationService
     */
    protected NavigationService $navigationService;

    /**
     * @param NavigationService $navigationService
     */
    public function injectNavigationService(NavigationService $navigationService)
    {
        $this->navigationService = $navigationService;
    }

    /**
     * List navigation
     *
     * @param Category|null $category
     */
    public function listAction(Category $category = null)
    {
        $this->view->assign('items', $this->navigationService->build($category, $this->settings));
    }
}
