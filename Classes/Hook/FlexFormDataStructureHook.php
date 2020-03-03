<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook;

use Pixelant\PxaProductManager\Configuration\Flexform\Registry;
use Pixelant\PxaProductManager\Configuration\Flexform\StructureLoader;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexFormDataStructureHook
 * @package Pixelant\PxaProductManager\Hook
 */
class FlexFormDataStructureHook implements SingletonInterface
{

    /**
     * Flexform identifier
     *
     * @var string
     */
    protected string $identifier = 'pxaproductmanager_pi1,list';

    /**
     * Last selected action
     *
     * @var string
     */
    protected ?string $lastSelectedAction = null;

    /**
     * @var FlexFormService
     */
    protected FlexFormService $service;

    /**
     * @var Registry
     */
    protected Registry $registry;

    /**
     * @var StructureLoader
     */
    protected StructureLoader $loader;

    /**
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * @param FlexFormService $service
     * @param Registry $registry
     * @param StructureLoader $loader
     */
    public function __construct(FlexFormService $service = null, Registry $registry = null, StructureLoader $loader = null)
    {
        $this->request = $GLOBALS['TYPO3_REQUEST'];
        $this->service = $service ?? GeneralUtility::makeInstance(FlexFormService::class);
        $this->registry = $registry ?? GeneralUtility::makeInstance(Registry::class);
        $this->loader = $loader ?? GeneralUtility::makeInstance(StructureLoader::class);
    }

    /**
     * Save last selected action
     *
     * @param array $fieldTCA
     * @param string $table
     * @param string $field
     * @param array $row
     * @param array $identifier
     * @return array
     */
    public function getDataStructureIdentifierPostProcess(
        array $fieldTCA,
        string $table,
        string $field,
        array $row,
        array $identifier
    ): array {
        if ($identifier['dataStructureKey'] === $this->identifier
            && is_string($row['pi_flexform'])
            && !empty($row['pi_flexform'])
        ) {
            $this->setLastActionFromSettings($row);
        }

        return $identifier;
    }

    /**
     * Modify product manager flexform structure
     *
     * @param array $dataStructure
     * @param array $identifier
     * @return array
     */
    public function parseDataStructureByIdentifierPostProcess(array $dataStructure, array $identifier)
    {
        if ($identifier['dataStructureKey'] === $this->identifier) {
            // Add action
            $dataStructure = $this->loader->defaultWithActionStructure(
                $dataStructure,
                $this->registry->getSwitchableControllerActionConfiguration($this->lastSelectedAction)
            );
        }

        return $dataStructure;
    }

    /**
     * Return last selected action. If ajax request read value from DB
     * @return string
     */
    protected function getLastSelectedAction(): string
    {
        // If this is ajax request '/ajax/record/tree/fetchData' then 'getDataStructureIdentifierPostProcess' wasn't called.
        // Need to read value of latest selected action from DB
        if ($this->lastSelectedAction === null && $this->request->getQueryParams()['uid']) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
            $queryBuilder->getRestrictions()->removeAll();

            $uid = (int)$this->request->getQueryParams()['uid'];
            $row = $queryBuilder
                ->select('pi_flexform')
                ->from('tt_content')
                ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)))
                ->execute()
                ->fetch();

            $this->setLastActionFromSettings($row);
        }

        return $this->lastSelectedAction;
    }

    /**
     * Return data structure with actions
     *
     * @param array $dataStructure
     * @return array
     */
    protected function addSwitchableControllerActions(array $dataStructure): array
    {
        $items = &$dataStructure['sheets']['sDEF']['ROOT']['el']['switchableControllerActions']['TCEforms']['config']['items'];

        foreach ($this->registry->getAllRegisteredActions() as $action) {
            $items[] = [
                $action['label'], $action['action']
            ];
        }

        return $dataStructure;
    }

    /**
     * Set last action from DB row flexform xml
     *
     * @param array $row
     */
    protected function setLastActionFromSettings(array $row): void
    {
        $flexformSettings = $this->service->convertFlexFormContentToArray($row['pi_flexform']);

        $this->lastSelectedAction = $flexformSettings['switchableControllerActions'];
    }
}
