<?php

/**
 * TechDivision\Import\Product\Variant\Ee\Observers\EeVariantObserver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */

namespace TechDivision\Import\Product\Variant\Ee\Observers;

use TechDivision\Import\Utils\StoreViewCodes;
use TechDivision\Import\Product\Utils\MemberNames;
use TechDivision\Import\Product\Variant\Utils\ColumnKeys;
use TechDivision\Import\Product\Variant\Observers\VariantObserver;

/**
 * A SLSB that handles the process to import product bunches.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/wagnert/csv-import
 * @link      http://www.appserver.io
 */
class EeVariantObserver extends VariantObserver
{

    /**
     * {@inheritDoc}
     * @see \Importer\Csv\Actions\Listeners\Row\ListenerInterface::handle()
     */
    public function handle(array $row)
    {

        // load the header information
        $headers = $this->getHeaders();

        // extract the parent/child ID as well as option value and variation label from the row
        $parentSku = $row[$headers[ColumnKeys::VARIANT_PARENT_SKU]];
        $childSku = $row[$headers[ColumnKeys::VARIANT_CHILD_SKU]];
        $optionValue = $row[$headers[ColumnKeys::VARIANT_OPTION_VALUE]];
        $variationLabel = $row[$headers[ColumnKeys::VARIANT_VARIATION_LABEL]];

        // load parent/child IDs
        $parentId = $this->mapSkuToRowId($parentSku);
        $childId = $this->mapSkuToEntityId($childSku);

        // create the product relation
        $this->persistProductRelation(array($parentId, $childId));
        $this->persistProductSuperLink(array($childId, $parentId));

        // load the store ID
        $store = $this->getStoreByStoreCode($row[$headers[ColumnKeys::STORE_VIEW_CODE]] ?: StoreViewCodes::ADMIN);
        $storeId = $store[MemberNames::STORE_ID];

        // load the EAV attribute
        $eavAttribute = $this->getEavAttributeByOptionValueAndStoreId($optionValue, $storeId);

        // query whether or not, the parent ID have changed
        if (!$this->isParentId($parentId)) {
            // preserve the parent ID
            $this->setParentId($parentId);

            // load the attribute ID
            $attributeId = $eavAttribute[MemberNames::ATTRIBUTE_ID];
            // if yes, create the super attribute load the ID of the new super attribute
            $productSuperAttributeId = $this->persistProductSuperAttribute(array($parentId, $attributeId, 0));

            // query whether or not we've to create super attribute labels
            if (empty($variationLabel)) {
                $variationLabel = $eavAttribute[MemberNames::FRONTENT_LABEL];
            }

            // prepare the super attribute label
            $params = array($productSuperAttributeId, $storeId, 0, $variationLabel);
            // save the super attribute label
            $this->persistProductSuperAttributeLabel($params);
        }

        // returns the row
        return $row;
    }

    /**
     * Return the row ID for the passed SKU.
     *
     * @param string $sku The SKU to return the row ID for
     *
     * @return integer The mapped row ID
     * @throws \Exception Is thrown if the SKU is not mapped yet
     */
    public function mapSkuToRowId($sku)
    {
        return $this->getSubject()->mapSkuToRowId($sku);
    }
}
