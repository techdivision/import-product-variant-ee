<?php

/**
 * TechDivision\Import\Product\Variant\Ee\Observers\EeCleanUpVariantProductRelationObserver
 *
 * PHP version 7
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Ee\Observers;

use TechDivision\Import\Product\Variant\Observers\CleanUpVariantProductRelationObserver;

/**
 * Observer that cleaned up a product's variant information.
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant
 * @link      http://www.techdivision.com
 */
class EeCleanUpVariantProductRelationObserver extends CleanUpVariantProductRelationObserver
{

    /**
     * Return's the PK to create the product => variant relation.
     *
     * @return integer The PK to create the relation with
     */
    protected function getLastPrimaryKey()
    {
        return $this->getLastRowId();
    }

    /**
     * Return's the row ID of the product that has been created recently.
     *
     * @return string The row Id
     */
    protected function getLastRowId()
    {
        return $this->getSubject()->getLastRowId();
    }
}
