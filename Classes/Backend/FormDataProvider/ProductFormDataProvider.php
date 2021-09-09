<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormDataProvider;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\FlashMessage\BackendFlashMessage;
use Pixelant\PxaProductManager\Translate\CanTranslateInBackend;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Form data provider hook, add TCA on a fly.
 */
class ProductFormDataProvider implements FormDataProviderInterface
{
    use CanTranslateInBackend;
    use CanCreateCollection;

    /**
     * @var DataMapper
     */
    protected DataMapper $dataMapper;

    /**
     * @var BackendFlashMessage
     */
    protected BackendFlashMessage $flashMessage;

    /**
     * @param BackendFlashMessage $flashMessage
     */
    public function __construct(BackendFlashMessage $flashMessage = null)
    {
        $this->flashMessage = $flashMessage ?? GeneralUtility::makeInstance(BackendFlashMessage::class);
    }

    /**
     * @param array $result
     * @return array
     */
    public function addData(array $result): array
    {
        if ($result['tableName'] !== ProductRepository::TABLE_NAME) {
            return $result;
        }

        $this->addCss();

        $result = $this->handleInheritedFields($result);

        return $result;
    }

    /**
     * Disable inherited fields.
     *
     * @param array $result
     * @return array
     */
    protected function handleInheritedFields(array $result)
    {
        $parentRow = $result['databaseRow']['parent'][0]['row'];

        if (!$result['databaseRow']['product_type'] || !$parentRow) {
            return $result;
        }

        $inheritFields = DataInheritanceUtility::getInheritedFieldsForProductType(
            (int)$result['databaseRow']['product_type'][0]
        );

        foreach ($result['processedTca']['columns'] as $fieldName => &$configuration) {
            if (!in_array($fieldName, $inheritFields, true)) {
                $configuration['config']['fieldWizard']['productParentValue']['renderType'] = 'productParentValue';

                continue;
            }

            if ($configuration['config']['type'] === 'inline') {
                // @codingStandardsIgnoreLine
                $result['processedTca']['ctrl']['container']['inline']['fieldInformation']['inheritedProductField']['renderType']
                    = 'inheritedProductField';
            } else {
                $configuration['config']['fieldInformation']['inheritedProductField']['renderType']
                    = 'inheritedProductField';
            }

            $configuration['config']['readOnly'] = true;
        }

        return $result;
    }

    /**
     * Add CSS to the page renderer. Ensures attributes are styles like other fields.
     */
    protected function addCss(): void
    {
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addCssFile(
            'EXT:pxa_product_manager/Resources/Public/Css/Backend/formEngine.css'
        );
    }
}
