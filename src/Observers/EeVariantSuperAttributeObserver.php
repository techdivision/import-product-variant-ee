<?php

/**
 * TechDivision\Import\Product\Variant\Ee\Observers\EeVariantSuperAttributeObserver
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant-ee
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Ee\Observers;

use TechDivision\Import\Product\Variant\Observers\VariantSuperAttributeObserver;

/**
 * Observer that provides extended mapping functionality to map a SKU to a row ID (EE Feature).
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant-ee
 * @link      http://www.techdivision.com
 */
class EeVariantSuperAttributeObserver extends VariantSuperAttributeObserver
{

    /**
     * Map's the passed SKU of the parent product to it's PK.
     *
     * @param string $parentSku The SKU of the parent product
     *
     * @return integer The primary key used to create relations
     */
    protected function mapParentSku($parentSku)
    {
        return $this->mapSkuToRowId($parentSku);
    }

    /**
     * Return the row ID for the passed SKU.
     *
     * @param string $sku The SKU to return the row ID for
     *
     * @return integer The mapped row ID
     * @throws \Exception Is thrown if the SKU is not mapped yet
     */
    protected function mapSkuToRowId($sku)
    {
        return $this->getSubject()->mapSkuToRowId($sku);
    }
}
