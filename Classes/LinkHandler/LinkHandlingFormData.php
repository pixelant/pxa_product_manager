<?php

namespace Pixelant\PxaProductManager\LinkHandler;

use Pixelant\PxaProductManager\Translate\CanTranslateInBackend;
use TYPO3\CMS\Backend\Form\Element\InputLinkElement;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class LinkHandlingFormData.
 */
class LinkHandlingFormData
{
    use CanTranslateInBackend;

    /**
     * @var IconFactory
     */
    protected IconFactory $iconFactory;

    /**
     * Initialization.
     *
     * @param IconFactory|null $iconFactory
     */
    public function __construct(IconFactory $iconFactory = null)
    {
        $this->iconFactory = $iconFactory ?? GeneralUtility::makeInstance(IconFactory::class);
    }

    /**
     * Data for link fields preview.
     *
     * @param array $linkData
     * @param array $linkParts
     * @param array $data
     * @param InputLinkElement $inputLinkElement
     * @return array
     */
    public function getFormData(
        array $linkData,
        array $linkParts,
        array $data,
        InputLinkElement $inputLinkElement
    ): array {
        if (isset($linkData['category']) || isset($linkData['product'])) {
            if (isset($linkData['product'])) {
                $table = 'tx_pxaproductmanager_domain_model_product';
                $id = $linkData['product'];
            } else {
                $table = 'sys_category';
                $id = $linkData['category'];
            }

            $row = BackendUtility::getRecord($table, $id);

            return [
                'text' => sprintf(
                    '%s[%d]',
                    BackendUtility::getRecordTitle($table, $row),
                    $id
                ),
                'icon' => $this->iconFactory->getIconForRecord($table, $row ?? [], Icon::SIZE_SMALL)->render(),
            ];
        }

        return [
            'text' => 'Category or product should be set for PM links',
            'icon' => '',
        ];
    }
}
