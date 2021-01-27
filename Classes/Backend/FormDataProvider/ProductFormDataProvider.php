<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormDataProvider;

use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ConfigurationProviderFactory;
use Pixelant\PxaProductManager\Attributes\ConfigurationProvider\ProviderInterface;
use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\AttributeSet;
use Pixelant\PxaProductManager\Domain\Model\AttributeValue;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Exception\NotImplementedException;
use Pixelant\PxaProductManager\FlashMessage\BackendFlashMessage;
use Pixelant\PxaProductManager\Translate\CanTranslateInBackend;
use Pixelant\PxaProductManager\Utility\AttributeTcaNamingUtility;
use Pixelant\PxaProductManager\Utility\DataInheritanceUtility;
use Pixelant\PxaProductManager\Utility\TcaUtility;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
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
        if ($result['tableName'] !== 'tx_pxaproductmanager_domain_model_product') {
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
            (int)$result['databaseRow']['product_type']
        );

        foreach ($result['processedTca']['columns'] as $fieldName => &$configuration) {
            if (!in_array($fieldName, $inheritFields, true)) {
                $configuration['config']['fieldWizard']['productParentValue']['renderType'] = 'productParentValue';

                continue;
            }

            $configuration['config']['readOnly'] = true;

            $configuration['config']['fieldInformation']['inheritedProductField']['renderType']
                = 'inheritedProductField';
        }

        return $result;
    }

    /**
     * Add CSS to the page renderer. Ensures attributes are styles like other fields.
     */
    protected function addCss()
    {
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addCssFile(
            'EXT:pxa_product_manager/Resources/Public/Css/Backend/formEngine.css'
        );
    }
}
