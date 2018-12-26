<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Storage;

use Pixelant\PxaProductManager\Domain\Model\Product;
use Pixelant\PxaProductManager\Utility\TCAUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser as ExtabaseTypo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Extends Extabase class in order to query attributes json field
 *
 * @package Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Storage
 */
class Typo3DbQueryParser extends ExtabaseTypo3DbQueryParser
{
    /**
     * Attributes value field name (camel case)
     *
     * @var string
     */
    protected $attributesValuesPropertyName = '';

    /**
     * Flag if query is from product manager
     *
     * @var bool
     */
    protected $isProductManagerQuery = false;

    /**
     * Check if query is for Product and mark flag if so
     *
     * @param QueryInterface $query
     * @return \TYPO3\CMS\Core\Database\Query\QueryBuilder
     */
    public function convertQueryToDoctrineQueryBuilder(QueryInterface $query)
    {
        $reflectionClass = new \ReflectionClass($query->getType());

        // Mark flag if type is product of subclass
        if ($reflectionClass->getName() === Product::class || $reflectionClass->isSubclassOf(Product::class)) {
            $this->isProductManagerQuery = true;
            $this->attributesValuesPropertyName = GeneralUtility::underscoredToLowerCamelCase(
                TCAUtility::ATTRIBUTES_VALUES_FIELD_NAME
            );
        }

        return parent::convertQueryToDoctrineQueryBuilder($query);
    }

    /**
     * Modify operand for attributes values
     * Otherwise if not an attributes values field return parent method result
     *
     * @param Qom\ComparisonInterface $comparison
     * @param Qom\SourceInterface $source
     * @return string
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Storage\Exception\BadConstraintException
     */
    protected function parseDynamicOperand(Qom\ComparisonInterface $comparison, Qom\SourceInterface $source)
    {
        $expr = parent::parseDynamicOperand($comparison, $source);

        if ($this->isProductManagerQuery
            && $this->attributesValuesPropertyName !== $comparison->getOperand1()->getPropertyName()
            && StringUtility::beginsWith(
                $comparison->getOperand1()->getPropertyName(),
                $this->attributesValuesPropertyName
            )
        ) {
            $delimiter = '->';
            // Extract attribute uid from field name like "attributesValues->1"
            list(, $attributeUid) = GeneralUtility::intExplode(
                $delimiter,
                $comparison->getOperand1()->getPropertyName(),
                true
            );

            // Get expression in parts in order to modify first part and insert JSON part
            $exprParts = GeneralUtility::trimExplode(' ', $expr, true);
            $exprPartsStarts = array_shift($exprParts);
            $exprPartsEnd = array_pop($exprParts);

            // Remove attribute UID from first part
            // `tx_pxaproductmanager_domain_model_product`.`attributes_values->1` become
            // `tx_pxaproductmanager_domain_model_product`.`attributes_values`
            $exprPartsStarts = str_replace($delimiter . $attributeUid, '', $exprPartsStarts);

            // TODO this work in mysql, what about others?
            // Add attribute UID json part
            $exprJsonPart = sprintf(
                '-> \'$."%s"\'',
                $attributeUid
            );

            // Gather expression again to get something like
            // `tx_pxaproductmanager_domain_model_product`.`attributes_values` -> '$."1"' = :dcValue1
            $expr = implode(
                ' ',
                array_merge(
                    [$exprPartsStarts, $exprJsonPart],
                    $exprParts,
                    [$exprPartsEnd]
                )
            );
        }

        return $expr;
    }
}
