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

use TechDivision\Import\Product\Variant\Utils\ColumnKeys;
use TechDivision\Import\Product\Variant\Utils\MemberNames;
use TechDivision\Import\Utils\EntityStatus;

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
     * Test's the handle() method successfull.
     *
     * @return void
     */
    public function testHandle()
    {

        $headers = array(
            ColumnKeys::VARIANT_PARENT_SKU => 0,
            ColumnKeys::VARIANT_CHILD_SKU  => 1
        );

        $row = array(
            0 => $parentSku = 'TEST-01',
            1 => $childSku = 'TEST-02'
        );

        // create a persist processor mock instance
        $mockSubject = $this->getMockBuilder('TechDivision\Import\Product\Variant\Ee\Subjects\EeVariantSubject')
                            ->setMethods(
                                array(
                                    'getHeader',
                                    'hasHeader',
                                    'getHeaders',
                                    'isDebugMode',
                                    'mapSkuToRowId',
                                    'mapSkuToEntityId',
                                    'persistProductRelation',
                                    'persistProductSuperLink',
                                    'getRow'
                                )
                            )
                            ->disableOriginalConstructor()
                            ->getMock();
        $mockSubject->expects($this->any())
                    ->method('mapSkuToRowId')
                    ->with($parentSku)
                    ->willReturn(1000);
        $mockSubject->expects($this->any())
                    ->method('mapSkuToEntityId')
                    ->with($childSku)
                    ->willReturn(1001);
        $mockSubject->expects($this->any())
                    ->method('getHeaders')
                    ->willReturn($headers);
        $mockSubject->expects($this->any())
                    ->method('getRow')
                    ->willReturn($row);
        $mockSubject->expects($this->any())
                    ->method('hasHeader')
                    ->willReturn(true);
        $mockSubject->expects($this->any())
                    ->method('isDebugMode')
                    ->willReturn(false);
        $mockSubject->expects($this->any())
                    ->method('getHeader')
                    ->withConsecutive(
                        array(ColumnKeys::VARIANT_PARENT_SKU),
                        array(ColumnKeys::VARIANT_CHILD_SKU)
                    )
                    ->willReturnOnConsecutiveCalls(0, 1);
        $mockSubject->expects($this->once())
                    ->method('persistProductRelation')
                    ->with(
                        array(
                            EntityStatus::MEMBER_NAME => EntityStatus::STATUS_CREATE,
                            MemberNames::PARENT_ID => 1000,
                            MemberNames::CHILD_ID  => 1001
                        )
                    )
                    ->willReturn(null);
        $mockSubject->expects($this->once())
                    ->method('persistProductSuperLink')
                    ->with(
                        array(
                            EntityStatus::MEMBER_NAME => EntityStatus::STATUS_CREATE,
                            MemberNames::PRODUCT_ID => 1001,
                            MemberNames::PARENT_ID => 1000
                        )
                    )
                    ->willReturn(null);

        // create a mock for the EE variant observer
        $mockObserver = $this->getMockBuilder('TechDivision\Import\Product\Variant\Ee\Observers\EeVariantObserver')
                             ->setMethods(array('getSubject'))
                             ->getMock();
        $mockObserver->expects($this->any())
                     ->method('getSubject')
                     ->willReturn($mockSubject);

        // test the mapSkuToRowId() method
        $this->assertSame($row, $mockObserver->handle($mockSubject));
    }
}
