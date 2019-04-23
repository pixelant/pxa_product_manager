<?php

namespace Pixelant\PxaProductManager\UserFunction;

use Pixelant\PxaProductManager\Service\Link\LinkBuilderService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class SolrProductLink
{
    /**
     * @var ContentObjectRenderer
     */
    public $cObj = null;

    /**
     * Generate link for solr
     *
     * @param string $content
     * @param array $params
     * @return string
     */
    public function getLink(/** @noinspection PhpUnusedParameterInspection */ $content, array $params): string
    {
        $pagePid = (int)($params['pageUid'] ?? 0);
        $productUid = (int)($this->cObj->data['uid'] ?? 0);
        $languageUid = (int)($this->cObj->data['__solr_index_language'] ?? 0);

        if ($pagePid === 0 || $productUid === 0) {
            throw new \UnexpectedValueException(
                '"$productUid" and "$pagePid" - could not be 0.',
                1517571016147
            );
        }

        return $this->getLinkBuilder($languageUid)->buildForProduct($pagePid, $productUid);
    }

    /**
     * Get link builder
     *
     * @param int $languageUid
     * @return LinkBuilderService
     */
    public function getLinkBuilder(int $languageUid): LinkBuilderService
    {
        return GeneralUtility::makeInstance(LinkBuilderService::class, $languageUid);
    }
}
