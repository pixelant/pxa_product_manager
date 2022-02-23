<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Controller;

use Pixelant\PxaProductManager\Configuration\Site\SettingsReader;
use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\DTO\CategoryDemand;
use Pixelant\PxaProductManager\Domain\Model\DTO\DemandInterface;
use Pixelant\PxaProductManager\Domain\Model\DTO\ProductDemand;
use Pixelant\PxaProductManager\Event\Controller\ExcludePagesFromMenuEvent;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

abstract class AbstractController extends ActionController
{
    use CanCreateCollection;

    /**
     * @var SettingsReader
     */
    protected SettingsReader $siteSettings;

    /**
     * @param SettingsReader $settingsReader
     */
    public function injectSettingsReader(SettingsReader $settingsReader): void
    {
        $this->siteSettings = $settingsReader;
    }

    /**
     * @var Dispatcher
     */
    protected Dispatcher $dispatcher;

    /**
     * Override plugin settings with site settings, before resolving view.
     * @return ViewInterface
     */
    protected function resolveView()
    {
        $singleViewPid = $this->siteSettings->getValue('singleViewPid');
        if ($singleViewPid) {
            $this->settings['pids']['singleViewPid'] = $singleViewPid;
        }

        return parent::resolveView();
    }

    /**
     * @param Dispatcher $dispatcher
     */
    public function injectDispatcher(Dispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Find records using repository by uids list.
     * Sort records according to given list.
     *
     * @param string $uidsList
     * @param Repository $repository
     * @return array
     */
    protected function findRecordsByList(string $uidsList, Repository $repository): array
    {
        $uids = GeneralUtility::intExplode(',', $uidsList, true);
        if (!empty($uids)) {
            $uids = $repository->overlayUidList($uids);
            $records = $repository->findByUids($uids)->toArray();

            return $this->collection($records)->sortByOrderList($uids, 'uid')->toArray();
        }

        return [];
    }

    /**
     * Create demand object using settings.
     *
     * @param array $settings
     * @param string $className
     * @return DemandInterface
     */
    protected function createDemandFromSettings(array $settings, string $className): DemandInterface
    {
        /** @var DemandInterface $demand */
        $demand = GeneralUtility::makeInstance($className);

        if (!empty($settings['limit'])) {
            $demand->setLimit((int)$settings['limit']);
        }
        if (!empty($settings['offSet'])) {
            $demand->setOffSet((int)$settings['offSet']);
        }
        if (!empty($settings['demand']['orderByAllowed'])) {
            $demand->setOrderByAllowed($settings['demand']['orderByAllowed']);
        }
        if (!empty($settings['orderBy'])) {
            $demand->setOrderBy($settings['orderBy']);
        }
        if (!empty($settings['orderDirection'])) {
            $demand->setOrderDirection($settings['orderDirection']);
        }

        $this->dispatcher->dispatch(__CLASS__, 'AfterDemandCreationBeforeReturn', [$demand, $settings]);

        return $demand;
    }

    /**
     * Create categories demand.
     *
     * @param array $settings
     * @param string $className
     * @return DemandInterface
     */
    protected function createCategoriesDemand(
        array $settings,
        string $className = CategoryDemand::class
    ): DemandInterface {
        $settings = array_merge($settings, $this->settings['categoriesOrderings'] ?? []);
        $className = $this->readFromSettings('demand.objects.categoryDemand', $className);

        $demand = $this->createDemandFromSettings($settings, $className);
        if (!empty($settings['navigation']['hideCategoriesWithoutProducts'])) {
            $demand->setHideCategoriesWithoutProducts((bool)$settings['navigation']['hideCategoriesWithoutProducts']);
        }
        if (!empty($settings['onlyVisibleInNavigation'])) {
            $demand->setOnlyVisibleInNavigation((bool)$settings['onlyVisibleInNavigation']);
        }
        if (!empty($settings['parent'])) {
            $demand->setParent($settings['parent']);
        }

        return $demand;
    }

    /**
     * Create products demand.
     *
     * @param array $settings
     * @param string $className
     * @return DemandInterface
     */
    protected function createProductsDemand(
        array $settings,
        string $className = ProductDemand::class
    ): DemandInterface {
        $settings = array_merge($settings, $this->settings['productOrderings'] ?? []);
        $className = $this->readFromSettings('demand.objects.productDemand', $className);

        $demand = $this->createDemandFromSettings($settings, $className);
        if (!empty($settings['categories'])) {
            $demand->setCategories($settings['categories']);
        }
        if (!empty($settings['categoryConjunction'])) {
            $demand->setCategoryConjunction($settings['categoryConjunction']);
        }
        $demand->setPageTreeStartingPoint(
            (int)$settings['pageTreeStartingPoint'] ?? $this->getTypoScriptFrontendController()->id
        );

        return $demand;
    }

    /**
     * Create order by options from settings.
     *
     * @return void
     */
    protected function createOrderByArray()
    {
        $orderBy = [];
        $listOrderBy = $this->settings['listView']['orderBy'];

        foreach ($listOrderBy as $listOrderByItem) {
            $text = LocalizationUtility::translate($listOrderByItem['key'], 'PxaProductManager');
            $orderBy[] = [
                'value' => $listOrderByItem['value'],
                'text' => $text,
            ];
        }

        return $orderBy;
    }

    /**
     * Read from settings by path or return default value.
     *
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    protected function readFromSettings(string $path, $default = null)
    {
        try {
            $value = ArrayUtility::getValueByPath($this->settings, $path, '.');
        } catch (MissingArrayPathException $exception) {
            $value = null;
        }

        return $value ?: $default;
    }

    /**
     * Get Typoscript frontend controller.
     *
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * Get number of menu levels
     *
     * @return int
     */
    protected function getMenuLevels(): int
    {
        return (int)$this->settings['menuLevels'] ?? 0;
    }

    /**
     * Get number of menu levels for list view
     *
     * @return int
     */
    protected function getListViewMenuLevels(): int
    {
        return (int)$this->settings['listView']['menuLevels'] ?? 0;
    }

    /**
     * Generate menu for fluid variable based on flexform settings.
     *
     * @return array
     */
    protected function generateMenu(): array
    {
        $levels = $this->getMenuLevels();
        $menuLevels = $this->getListViewMenuLevels();
        if ($menuLevels > 0 && $menuLevels < 4) {
            $levels = $menuLevels;
        }

        $data = [];

        if ((int)$levels > 0) {
            $currentPageId = $this->getTypoScriptFrontendController()->id;
            $rootLine = $this->getTypoScriptFrontendController()->rootLine;

            $fixedMenuPageId = (int)$this->settings['listView']['menuPageId'] ?? 0;
            if ($fixedMenuPageId > 0) {
                $currentPageId = $fixedMenuPageId;
            }

            $subpages = $this->getMenuOfSubpages($currentPageId, $levels);
            // Check if we are on "last level" step one page up in menu.
            if (empty($subpages)) {
                $parentPageId = $rootLine[count($rootLine) - 2]['uid'] ?? false;
                if (!empty($parentPageId)) {
                    $currentPageId = (int)$parentPageId;
                    $subpages = $this->getMenuOfSubpages($currentPageId, $levels);
                }
            }

            $current = $this->getMenuOfCurrentPage($currentPageId);

            $data = $current[0] ?? [];
            $data['children'] = $subpages ?? [];
        }

        return $data;
    }

    /**
     * Generate menu for fluid variable based on flexform settings.
     *
     * @param int $pageId
     * @param int $levels
     * @return array
     */
    protected function getMenuOfSubpages(int $pageId, int $levels): array
    {
        $menuDirectoryProcessor = GeneralUtility::makeInstance(
            \TYPO3\CMS\Frontend\DataProcessing\MenuProcessor::class
        );

        $excludeUidList = $this->settings['listView']['excludeUidList'] ?? '';

        $excludePagesFromMenuEvent = GeneralUtility::makeInstance(ExcludePagesFromMenuEvent::class, $excludeUidList);
        $event = $this->eventDispatcher->dispatch($excludePagesFromMenuEvent);
        $excludeUidList = $event->getExcludeUidList();

        return $menuDirectoryProcessor->process(
            $this->configurationManager->getContentObject(),
            [],
            [
                'special' => 'directory',
                'special.' => ['value' => $pageId],
                'levels' => $levels,
                'as' => 'subpages',
                'excludeUidList' => $excludeUidList,
            ],
            []
        )['subpages'] ?? [];
    }

    /**
     * Generate menu for fluid variable based on flexform settings.
     *
     * @param int $pageId
     * @return array
     */
    protected function getMenuOfCurrentPage(int $pageId): array
    {
        $menuListProcessor = GeneralUtility::makeInstance(
            \TYPO3\CMS\Frontend\DataProcessing\MenuProcessor::class
        );

        return $menuListProcessor->process(
            $this->configurationManager->getContentObject(),
            [],
            [
                'special' => 'list',
                'special.' => ['value' => $pageId],
                'as' => 'current',
            ],
            []
        )['current'] ?? [];
    }
}
