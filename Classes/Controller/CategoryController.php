<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Domain\Model\Category;
use Pixelant\PxaProductManager\Domain\Model\DTO\CategoryDemand;
use Pixelant\PxaProductManager\Domain\Model\DTO\Factory\CategoryDemandFactory;
use Pixelant\PxaProductManager\Domain\Repository\CategoryRepository;
use Pixelant\PxaProductManager\Service\NavigationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Categories controller
 */
class CategoryController extends ActionController
{

    /**
     * List navigation
     *
     * @param Category|null $category
     */
    public function listAction(Category $category = null)
    {
        $this->view->assign('items', $this->navigationService->build($category, $this->settings));
    }

    protected function getNavigationService(): NavigationService
    {
        $service = GeneralUtility::makeInstance(NavigationService::class);
        if ($this->settings[''])
    }
}
