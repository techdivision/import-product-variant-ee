<?php

/**
 * TechDivision\Import\Product\Variant\Ee\Observers\EeVariantObserverTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-media-ee
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Product\Variant\Ee\Observers;

/**
 * Test class for the EE variant observer implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-product-media-ee
 * @link      http://www.techdivision.com
 */
class EeVariantObserverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test's the mapSkuToRowId() method successfull.
     *
     * @return void
     */
    public function testMapSkuToRowId()
    {

        // create a persist processor mock instance
        $mockSubject = $this->getMock('TechDivision\Import\Product\Variant\Ee\Subjects\EeVariantSubject');
        $mockSubject->expects($this->once())
                    ->method('mapSkuToRowId')
                    ->with($sku = 'TEST-01')
                    ->willReturn($rowId = 1000);

        // create a mock for the EE variant observer
        $mockObserver = $this->getMockBuilder('TechDivision\Import\Product\Variant\Ee\Observers\EeVariantObserver')
                           ->setMethods(array('getSubject'))
                           ->getMock();
                           $mockObserver->expects($this->once())
                   ->method('getSubject')
                   ->willReturn($mockSubject);

        // test the mapSkuToRowId() method
        $this->assertSame($rowId, $mockObserver->mapSkuToRowId($sku));
    }
}
