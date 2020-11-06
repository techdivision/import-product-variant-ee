<?php

/**
 * TechDivision\Import\Product\Variant\Ee\Observers\EeCleanUpVariantProductRelationObserver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-media
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Ee\Observers;

use TechDivision\Import\Product\Ee\Utils\MemberNames;
use TechDivision\Import\Product\Variant\Observers\CleanUpVariantProductRelationObserver;

/**
 * Observer that cleaned up a product's media gallery information.
 *
 * @author    Martin Eisenführer <m.eisenfuehrer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-media
 * @link      http://www.techdivision.com
 */
class EeCleanUpVariantProductRelationObserver extends CleanUpVariantProductRelationObserver
{

    /**
     * Return's the primary key of the product to load.
     *
     * @param array $product product array like from ProductBunchProcessorInterface::loadProduct
     * @return integer The primary key of the product
     */
    protected function getPrimaryKey(array $product)
    {
        return isset($product[MemberNames::ROW_ID]) ? $product[MemberNames::ROW_ID] : null;
    }
}
