<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormDataProvider;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Repository\AttributeValueRepository;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\FlashMessage\BackendFlashMessage;
use Pixelant\PxaProductManager\Translate\CanTranslateInBackend;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Form data provider hook, add TCA on a fly.
 */
class AttributeValueFormDataProvider implements FormDataProviderInterface
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
        if ($result['tableName'] !== AttributeValueRepository::TABLE_NAME) {
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
        $attributeValue = $result['databaseRow'];

        $product = BackendUtility::getRecord(
            ProductRepository::TABLE_NAME,
            $attributeValue['product']
        );

        if (!$product['product_type'] || !$product['parent']) {
            return $result;
        }

        $configuration = &$result['processedTca']['columns']['value'];

        if (in_array(
            'attribute.' . $attributeValue['attribute'][0],
            DataInheritanceUtility::getInheritedFieldsForProductType((int)$product['product_type']),
            true
        )) {
            $configuration['config']['readOnly'] = true;

            if ($configuration['config']['type'] === 'inline') {
                $result['processedTca']['ctrl']['container']['inline']['fieldInformation']['inheritedProductField']['renderType']
                    = 'inheritedProductField';
            } else {
                $configuration['config']['fieldInformation']['inheritedProductField']['renderType']
                    = 'inheritedProductField';
            }
        } else {
            if ($configuration['config']['type'] === 'inline') {
                $result['processedTca']['ctrl']['container']['inline']['fieldWizard']['productParentValue']['renderType']
                    = 'productParentValue';
            } else {
                $configuration['config']['fieldWizard']['productParentValue']['renderType']
                    = 'productParentValue';
            }
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
