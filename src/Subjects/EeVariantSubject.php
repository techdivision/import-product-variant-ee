<?php

/**
 * TechDivision\Import\Product\Variant\Ee\Subjects\EeVariantSubject
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant-ee
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Ee\Subjects;

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Product\Ee\Subjects\EeBunchSubject;
use TechDivision\Import\Product\Variant\Subjects\VariantSubjectTrait;

/**
 * A subject that handles the process to import product variants.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-product-variant-ee
 * @link      http://www.techdivision.com
 */
class EeVariantSubject extends EeBunchSubject
{

    /**
     * The trait that provides the functionality to import variants on subject level.
     *
     * @var \TechDivision\Import\Product\Variant\Subjects\VariantSubjectTrait
     */
    use VariantSubjectTrait;

    /**
     * Intializes the previously loaded global data for exactly one variants.
     *
     * @param string $serial The serial of the actual import
     *
     * @return void
     */
    public function setUp($serial)
    {

        // invoke the parent method
        parent::setUp($serial);

        // load the entity manager and the registry processor
        $registryProcessor = $this->getRegistryProcessor();

        // load the status of the actual import process
        $status = $registryProcessor->getAttribute(RegistryKeys::STATUS);

        // load the SKU => row/entity ID mapping
        $this->skuRowIdMapping = $status[RegistryKeys::SKU_ROW_ID_MAPPING];
        $this->skuEntityIdMapping = $status[RegistryKeys::SKU_ENTITY_ID_MAPPING];
    }
}
