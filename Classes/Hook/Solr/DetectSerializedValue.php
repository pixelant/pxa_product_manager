<?php

declare(strict_types=1);

namespace Pixelant\PxaProductManager\Hook\Solr;

use ApacheSolrForTypo3\Solr\IndexQueue\SerializedValueDetector;
use Pixelant\PxaProductManager\UserFunction\Solr\AttributeMultiValue;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook registered for function inext/solr/Classes/IndexQueue/AbstractIndexer.php
 * where indexer decides if value needs to be unserialized, e.g. _stringM fields.
 */
class DetectSerializedValue implements SerializedValueDetector
{
    /**
     * Uses a field's configuration to detect whether its value returned by a
     * content object is expected to be serialized and thus needs to be
     * unserialized.
     *
     * @param array $indexingConfiguration Current item's indexing configuration
     * @param string $solrFieldName Current field being indexed
     * @return bool TRUE if the value is expected to be serialized, FALSE otherwise
     */
    public function isSerializedValue(array $indexingConfiguration, $solrFieldName): bool
    {
        $userFunc = $indexingConfiguration[$solrFieldName . '.']['userFunc'];
        if (empty($userFunc)) {
            return false;
        }

        if (GeneralUtility::trimExplode('->', $userFunc)[0] === AttributeMultiValue::class) {
            return true;
        }

        return false;
    }
}
