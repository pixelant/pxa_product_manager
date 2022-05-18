<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\FormDataProvider;

use Pixelant\PxaProductManager\Domain\Collection\CanCreateCollection;
use Pixelant\PxaProductManager\Domain\Repository\AttributeRepository;
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
        $attributeUidList = array_column($attributes, 'uid');

        $productLanguageFieldName = $GLOBALS['TCA'][ProductRepository::TABLE_NAME]['ctrl']['languageField'];
        $sysLanguageUid = isset(
            $result['databaseRow'][$productLanguageFieldName]['0']
        ) ? (int)$result['databaseRow'][$productLanguageFieldName]['0'] : 0;

        $attrLangField = $GLOBALS['TCA'][AttributeRepository::TABLE_NAME]['ctrl']['languageField'] ?? null;

        // don't display attributevalues for attributes not included for product type
        foreach ($result['processedTca']['columns']['attributes_values']['children'] as $key => $attributeValueResult) {
            if (!in_array((int)$attributeValueResult['databaseRow']['attribute'], $attributeUidList, true)) {
                unset($result['processedTca']['columns']['attributes_values']['children'][$key]);
            } else {
                // Make sure attributevalue has same language as edited product.
                if (
                    isset($attributeValueResult['databaseRow'][$attrLangField][0])
                    && (int)$attributeValueResult['databaseRow'][$attrLangField][0] !== $sysLanguageUid
                ) {
                    $attributeValueResult['databaseRow'][$attrLangField][0] = (string)$sysLanguageUid;
                }
            }
        }

        foreach ($attributes as $attribute) {
            foreach ($result['processedTca']['columns']['attributes_values']['children'] as $attributeValueResult) {
                if ((int)$attributeValueResult['databaseRow']['attribute'] === $attribute['uid']) {
                    // Skip this $attribute if it exists in the record
                    continue 2;
                }
            }

            $newChild = $this->generateNewAttributeValueChild($result, $attribute);
            $result['processedTca']['columns']['attributes_values']['children'][] = $newChild;
        }

        return $result;
    }

    /**
     * Generate New AttributeValue Child.
     *
     * @param array $result
     * @param array $attribute
     * @return array
     */
    protected function generateNewAttributeValueChild(array $result, array $attribute)
    {
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
            // 'inlineParentFieldName' => $parentFieldName,
            'inlineParentFieldName' => 'attributes_values',

            // values of the top most parent element set on first level and not overridden on following levels
            'inlineTopMostParentUid' => $result['inlineTopMostParentUid'] ?: $inlineTopMostParent['uid'] ?? 0,

            // @codingStandardsIgnoreLine
            'inlineTopMostParentTableName' => $result['inlineTopMostParentTableName'] ?: $inlineTopMostParent['table'] ?? '',
            // @codingStandardsIgnoreLine
            'inlineTopMostParentFieldName' => $result['inlineTopMostParentFieldName'] ?: $inlineTopMostParent['field'] ?? '',

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

        return $newChild;
    }
}
