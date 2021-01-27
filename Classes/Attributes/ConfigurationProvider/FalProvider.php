<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Attributes\ConfigurationProvider;

use Pixelant\PxaProductManager\Domain\Model\Attribute;
use Pixelant\PxaProductManager\Utility\AttributeTcaNamingUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Fal configuration.
 */
class FalProvider implements ProviderInterface
{
    /**
     * @var array
     */
    protected array $attribute;

    /**
     * @param array $attribute
     */
    public function __construct(array $attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * Return TCA configuration.
     *
     * @return array
     */
    public function get(): array
    {
        if ($this->attribute['type'] === Attribute::ATTRIBUTE_TYPE_IMAGE) {
            $allowedFileTypes = $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'];
            $label = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference';
        } else {
            $allowedFileTypes = '';
            $label = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:media.addFileReference';
        }

        $configuration = $this->getFalFieldTCAConfiguration(
            $label,
            $allowedFileTypes
        );

        $configuration['label'] = $this->attribute['name'];

        return $configuration;
    }

    /**
     * Fal dynamic configuration.
     *
     * @param string $addNewLabel
     * @param string $allowedFileExtensions
     * @param string $disallowedFileExtensions
     * @return array
     */
    protected function getFalFieldTCAConfiguration(
        string $addNewLabel = '',
        string $allowedFileExtensions = '',
        string $disallowedFileExtensions = ''
    ): array {
        return [
            'exclude' => false,
            'label' => '',
            // @codingStandardsIgnoreStart
            'config' => ExtensionManagementUtility::getFileFieldTCAConfig(
                'value',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => $addNewLabel,
                        'showPossibleLocalizationRecords' => false,
                        'showRemovedLocalizationRecords' => true,
                        'showAllLocalizationLink' => false,
                        'showSynchronizationLink' => false,
                        'collapseAll' => true,
                    ],
                    'foreign_match_fields' => [
                        'fieldname' => 'value',
                        'tablenames' => 'tx_pxaproductmanager_domain_model_attributevalue',
                        'table_local' => 'sys_file',
                    ],
                    'behaviour' => [
                        'allowLanguageSynchronization' => true,
                    ],
                    'overrideChildTca' => [
                        /*'columns' => [
                            'pxa_attribute' => [
                                'config' => [
                                    'default' => $this->attribute,
                                ],
                            ],
                        ],*/
                        'types' => [
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_UNKNOWN => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerAttributePalette,
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerAttributePalette,
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerAttributePalette,
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerAttributePalette,
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.audioOverlayPalette;audioOverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerAttributePalette,
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.videoOverlayPalette;videoOverlayPalette,
                            --palette--;;filePalette',
                            ],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                                'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;pxaProductManagerAttributePalette,
                            --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.basicoverlayPalette;basicoverlayPalette,
                            --palette--;;filePalette',
                            ],
                        ],
                    ],
                    'maxitems' => 99,
                ],
                $allowedFileExtensions,
                $disallowedFileExtensions
            ),
            // @codingStandardsIgnoreEnd
        ];
    }
}
