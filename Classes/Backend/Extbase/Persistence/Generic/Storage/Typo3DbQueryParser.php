<?php
declare(strict_types=1);

namespace Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Storage;

use Pixelant\PxaProductManager\Backend\Extbase\Persistence\Generic\Qom\AttributesRange;
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
     * Initialize
     */
    public function __construct()
    {
        $this->attributesValuesPropertyName = GeneralUtility::underscoredToLowerCamelCase(
            TCAUtility::ATTRIBUTES_VALUES_FIELD_NAME
        );
    }

    /**
     * Parse additional products constraints
     *
     * @param Qom\ConstraintInterface $constraint
     * @param Qom\SourceInterface $source
     * @return string|\TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression
     */
    protected function parseConstraint(Qom\ConstraintInterface $constraint, Qom\SourceInterface $source)
    {
        // If is attributes range, create join options table uid on attributes_values json field
        // between range values of options
        if ($constraint instanceof AttributesRange) {
            list($propertyName, $attributeUid) = $this->fetchPropertyNameAndAttributeUid(
                $constraint->getOperand()->getPropertyName()
            );

            if (!$source instanceof Qom\SelectorInterface) {
                throw new \RuntimeException('Source is not of type "SelectorInterface"', 1395362539);
            }

            // Nothing to do if no values
            if ($constraint->getMin() === null && $constraint->getMax() === null) {
                return '';
            }

            $className = $source->getNodeTypeName();
            $tableName = $this->dataMapper->convertClassNameToTableName($className);
            $columnName = $this->dataMapper->convertPropertyNameToColumnName($propertyName, $className);

            $alias = $this->getUniqueAlias('tx_pxaproductmanager_domain_model_option', (string)$attributeUid);
            $this->queryBuilder->join(
                $tableName,
                'tx_pxaproductmanager_domain_model_option',
                $alias,
                sprintf(
                    'FIND_IN_SET(%s.uid, %s)',
                    $alias,
                    sprintf(
                        '%s ->> \'$."%d"\'',
                        $this->queryBuilder->quoteIdentifier($tableName . '.' . $columnName),
                        $attributeUid
                    )
                )
            );

            $minMaxConstraints = [];
            if ($constraint->getMin() !== null) {
                $minMaxConstraints[] = $this->queryBuilder->expr()->gte(
                    $alias . '.value',
                    $this->queryBuilder->createNamedParameter((int)$constraint->getMin(), \PDO::PARAM_INT)
                );
            }
            if ($constraint->getMax() !== null) {
                $minMaxConstraints[] = $this->queryBuilder->expr()->lte(
                    $alias . '.value',
                    $this->queryBuilder->createNamedParameter((int)$constraint->getMax(), \PDO::PARAM_INT)
                );
            }

            $this->queryBuilder->andWhere(...$minMaxConstraints);

            return '';
        } else {
            return parent::parseConstraint($constraint, $source);
        }
    }

    /**
     * If "contains" criterion used for attributes_values return simple FIND_IN_SET
     * @TODO support of not mysql?
     *
     *
     * @param Qom\ComparisonInterface $comparison
     * @param Qom\SourceInterface $source
     * @return string
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnexpectedTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Storage\Exception\BadConstraintException
     */
    protected function parseComparison(Qom\ComparisonInterface $comparison, Qom\SourceInterface $source)
    {
        $propertyName = $comparison->getOperand1()->getPropertyName();

        // If operator contains for attributes_values JSON field
        if ($comparison->getOperator() === QueryInterface::OPERATOR_CONTAINS
            && $this->isAttributesValuesJsonProperty($propertyName)
        ) {
            if (!$source instanceof Qom\SelectorInterface) {
                throw new \RuntimeException('Source is not of type "SelectorInterface"', 1395362539);
            }

            $className = $source->getNodeTypeName();
            $tableName = $this->dataMapper->convertClassNameToTableName($className);
            list($propertyName, $attributeUid) = $this->fetchPropertyNameAndAttributeUid($propertyName);
            $columnName = $this->dataMapper->convertPropertyNameToColumnName($propertyName, $className);

            $value = $this->dataMapper->getPlainValue($comparison->getOperand2());

            $expr = sprintf(
                'FIND_IN_SET(%s, %s)',
                $value,
                sprintf(
                    '%s ->> \'$."%d"\'',
                    $this->queryBuilder->quoteIdentifier($tableName . '.' . $columnName),
                    $attributeUid
                )
            );

            return $expr;
        }

        // Execute parent
        return parent::parseComparison($comparison, $source);
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
        $propertyName = $comparison->getOperand1()->getPropertyName();

        if ($this->isAttributesValuesJsonProperty($propertyName)) {
            // Extract attribute uid from field name like "attributesValues->1"
            list(, $attributeUid) = $this->fetchPropertyNameAndAttributeUid($propertyName);

            // Get expression in parts in order to modify first part and insert JSON part
            $exprParts = GeneralUtility::trimExplode(' ', $expr, true);
            $exprPartsStarts = array_shift($exprParts);

            // Remove attribute UID from first part
            // `tx_pxaproductmanager_domain_model_product`.`attributes_values->1` become
            // `tx_pxaproductmanager_domain_model_product`.`attributes_values`
            $exprPartsStarts = str_replace($this->getDelimiter() . $attributeUid, '', $exprPartsStarts);

            // TODO this work in mysql, what about others?
            // Add attribute UID json part
            $exprJsonPart = sprintf(
                '-> \'$."%d"\'',
                $attributeUid
            );

            // Gather expression again to get something like
            // `tx_pxaproductmanager_domain_model_product`.`attributes_values` -> '$."1"' = :dcValue1
            $expr = implode(
                ' ',
                array_merge(
                    [$exprPartsStarts, $exprJsonPart],
                    $exprParts
                )
            );
        }

        return $expr;
    }

    /**
     * Check if property is product manager JSON field
     *
     * @param string $propertyName
     * @return bool
     */
    private function isAttributesValuesJsonProperty(string $propertyName): bool
    {
        return $this->attributesValuesPropertyName !== $propertyName // is not exactly property name
            && StringUtility::beginsWith(
                $propertyName,
                $this->attributesValuesPropertyName
            );
    }

    /**
     * Fetch property name and attribute uid from given attribute values property name
     *
     * @param string $propertyName
     * @return array
     */
    private function fetchPropertyNameAndAttributeUid(string $propertyName): array
    {
        list($property, $uid) = GeneralUtility::trimExplode(
            $this->getDelimiter(),
            $propertyName,
            true
        );

        return [$property, (int)$uid];
    }

    /**
     * Return property delimiter
     * attributesValues->attributeUid
     *
     * @return string
     */
    private function getDelimiter(): string
    {
        return '->';
    }
}
