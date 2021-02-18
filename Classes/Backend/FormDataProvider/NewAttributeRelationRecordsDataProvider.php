<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormDataProvider;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Domain\Repository\ProductRepository;
use Pixelant\PxaProductManager\FlashMessage\BackendFlashMessage;
use Pixelant\PxaProductManager\Translate\CanTranslateInBackend;
use Pixelant\PxaProductManager\Utility\AttributeUtility;
use TYPO3\CMS\Backend\Form\FormDataCompiler;
use TYPO3\CMS\Backend\Form\FormDataGroup\TcaDatabaseRecord;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Form\InlineStackProcessor;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;

/**
 * Adds any missing attributes to a product record we're editing.
 */
class NewAttributeRelationRecordsDataProvider implements FormDataProviderInterface
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
        if (
            $result['tableName'] !== ProductRepository::TABLE_NAME
            || (
                $result['tableName'] === ProductRepository::TABLE_NAME
                && !$result['databaseRow']['product_type']
            )
        ) {
            return $result;
        }

        $attributes = AttributeUtility::findAttributesForProductType((int)$result['databaseRow']['product_type'][0]);

        // don't display attributevalues for attributes not included for product type
        foreach ($result['processedTca']['columns']['attributes_values']['children'] as $key => $attributeValueResult) {
            $attributeStillExist = false;
            foreach ($attributes as $attribute) {
                if ((int)$attributeValueResult['databaseRow']['attribute'][0] === $attribute['uid']) {
                    $attributeStillExist = true;

                    continue;
                }
            }
            if (!$attributeStillExist) {
                unset($result['processedTca']['columns']['attributes_values']['children'][$key]);
            }
        }

        foreach ($attributes as $attribute) {
            foreach ($result['processedTca']['columns']['attributes_values']['children'] as $attributeValueResult) {
                if ((int)$attributeValueResult['databaseRow']['attribute'][0] === $attribute['uid']) {
                    // Skip this $attribute if it exists in the record
                    continue 2;
                }
            }

            $parentConfig = $result['processedTca']['columns']['attributes_values']['config'];
            $childTableName = $parentConfig['foreign_table'];

            /** @var InlineStackProcessor $inlineStackProcessor */
            $inlineStackProcessor = GeneralUtility::makeInstance(InlineStackProcessor::class);
            $inlineStackProcessor->initializeByGivenStructure($result['inlineStructure']);
            $inlineTopMostParent = $inlineStackProcessor->getStructureLevel(0);

            /** @var TcaDatabaseRecord $formDataGroup */
            $formDataGroup = GeneralUtility::makeInstance(TcaDatabaseRecord::class);
            /** @var FormDataCompiler $formDataCompiler */
            $formDataCompiler = GeneralUtility::makeInstance(FormDataCompiler::class, $formDataGroup);
            // This is mostly copied from \TYPO3\CMS\Backend\Form\FormDataProvider\TcaInline
            $formDataCompilerInput = [
                'command' => 'new',
                'tableName' => $childTableName,
                // Give incoming returnUrl down to children so they generate a returnUrl back to
                // the originally opening record, also see "originalReturnUrl" in inline container
                // and FormInlineAjaxController
                'returnUrl' => $result['returnUrl'],
                'isInlineChild' => true,
                'inlineStructure' => $result['inlineStructure'],
                'inlineExpandCollapseStateArray' => $result['inlineExpandCollapseStateArray'],
                'inlineFirstPid' => $result['inlineFirstPid'],
                'inlineParentConfig' => $parentConfig,

                // values of the current parent element
                // it is always a string either an id or new...
                'inlineParentUid' => $result['databaseRow']['uid'],
                'inlineParentTableName' => $result['tableName'],
                'inlineParentFieldName' => $parentFieldName,

                // values of the top most parent element set on first level and not overridden on following levels
                'inlineTopMostParentUid' => $result['inlineTopMostParentUid'] ?: $inlineTopMostParent['uid'],

                // @codingStandardsIgnoreLine
                'inlineTopMostParentTableName' => $result['inlineTopMostParentTableName'] ?: $inlineTopMostParent['table'],
                // @codingStandardsIgnoreLine
                'inlineTopMostParentFieldName' => $result['inlineTopMostParentFieldName'] ?: $inlineTopMostParent['field'],

                'recordTypeValue' => $attribute['uid'],
                'databaseRow' => [
                    'attribute' => [$attribute['uid']],
                ],
            ];

            // For foreign_selector with useCombination $mainChild is the mm record
            // and $combinationChild is the child-child. For 1:n "normal" relations,
            // $mainChild is just the normal child record and $combinationChild is empty.
            $newChild = $formDataCompiler->compile($formDataCompilerInput);

            // This wizard sets the attribute type
            if ($newChild['processedTca']['columns']['value']['config']['type'] === 'inline') {
                // @codingStandardsIgnoreLine
                $newChild['processedTca']['ctrl']['container']['inline']['fieldWizard']['hiddenAttributeType']['renderType']
                    = 'hiddenAttributeType';
            } else {
                // @codingStandardsIgnoreLine
                $newChild['processedTca']['columns']['value']['config']['fieldWizard']['hiddenAttributeType']['renderType']
                    = 'hiddenAttributeType';
            }

            $result['processedTca']['columns']['attributes_values']['children'][] = $newChild;
        }

        return $result;
    }
}
