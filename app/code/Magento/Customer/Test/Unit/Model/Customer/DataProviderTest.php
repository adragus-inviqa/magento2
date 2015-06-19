<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Test\Unit\Model\Customer;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Type;
use Magento\Ui\DataProvider\EavValidationRules;
use Magento\Customer\Model\Customer\DataProvider;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Customer\Model\Resource\Customer\CollectionFactory;

/**
 * Class DataProviderTest
 *
 * Test for class \Magento\Customer\Model\Customer\DataProvider
 */
class DataProviderTest extends \PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_CODE = 'test-code';
    const OPTIONS_RESULT = 'test-options';

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfigMock;

    /**
     * @var CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerCollectionFactoryMock;

    /**
     * @var EavValidationRules|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavValidationRulesMock;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->eavConfigMock = $this->getMockBuilder('Magento\Eav\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerCollectionFactoryMock = $this->getMockBuilder(
            'Magento\Customer\Model\Resource\Customer\CollectionFactory'
        )->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->eavValidationRulesMock = $this->getMockBuilder('Magento\Ui\DataProvider\EavValidationRules')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Run test getAttributesMeta method
     *
     * @param array $expected
     * @return void
     *
     * @dataProvider getAttributesMetaDataProvider
     */
    public function testGetAttributesMetaWithOptions(array $expected)
    {
        $dataProvider = new DataProvider(
            'test-name',
            'primary-field-name',
            'request-field-name',
            $this->eavValidationRulesMock,
            $this->getCustomerCollectionFactoryMock(),
            $this->getEavConfigMock()
        );

        $meta = $dataProvider->getMeta();
        $this->assertNotEmpty($meta);
        $this->assertEquals($expected, $meta);
    }

    /**
     * Data provider for testGetAttributesMeta
     *
     * @return array
     */
    public function getAttributesMetaDataProvider()
    {
        return [
            [
                'expected' => [
                    'customer' => [
                        'fields' => [
                            self::ATTRIBUTE_CODE => [
                                'dataType' => 'frontend_input',
                                'formElement' => 'frontend_input',
                                'options' => 'test-options',
                                'visible' => 'is_visible',
                                'required' => 'is_required',
                                'label' => 'frontend_label',
                                'sortOrder' => 'sort_order',
                                'notice' => 'note',
                                'default' => 'default_value',
                                'size' => 'multiline_count',
                            ]
                        ]
                    ],
                    'address' => [
                        'fields' => [
                            self::ATTRIBUTE_CODE => [
                                'dataType' => 'frontend_input',
                                'formElement' => 'frontend_input',
                                'options' => 'test-options',
                                'visible' => 'is_visible',
                                'required' => 'is_required',
                                'label' => 'frontend_label',
                                'sortOrder' => 'sort_order',
                                'notice' => 'note',
                                'default' => 'default_value',
                                'size' => 'multiline_count',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCustomerCollectionFactoryMock()
    {
        $collectionMock = $this->getMockBuilder('Magento\Customer\Model\Resource\Customer\Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $collectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->with('*');

        $this->customerCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        return $this->customerCollectionFactoryMock;
    }

    /**
     * @return Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEavConfigMock()
    {
        $this->eavConfigMock->expects($this->at(0))
            ->method('getEntityType')
            ->with('customer')
            ->willReturn($this->getTypeCustomerMock());
        $this->eavConfigMock->expects($this->at(1))
            ->method('getEntityType')
            ->with('customer_address')
            ->willReturn($this->getTypeAddressMock());

        return $this->eavConfigMock;
    }

    /**
     * @return Type|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTypeCustomerMock()
    {
        $typeCustomerMock = $this->getMockBuilder('Magento\Eav\Model\Entity\Type')
            ->disableOriginalConstructor()
            ->getMock();

        $typeCustomerMock->expects($this->once())
            ->method('getAttributeCollection')
            ->willReturn($this->getAttributeMock());

        return $typeCustomerMock;
    }

    /**
     * @return Type|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTypeAddressMock()
    {
        $typeAddressMock = $this->getMockBuilder('Magento\Eav\Model\Entity\Type')
            ->disableOriginalConstructor()
            ->getMock();

        $typeAddressMock->expects($this->once())
            ->method('getAttributeCollection')
            ->willReturn($this->getAttributeMock());

        return $typeAddressMock;
    }

    /**
     * @return AbstractAttribute[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    protected function getAttributeMock()
    {
        $attributeMock = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\AbstractAttribute')
            ->setMethods(['getAttributeCode', 'getDataUsingMethod', 'usesSource', 'getSource'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $sourceMock = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\Source\AbstractSource')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $sourceMock->expects($this->any())
            ->method('getAllOptions')
            ->willReturn(self::OPTIONS_RESULT);

        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn(self::ATTRIBUTE_CODE);

        $attributeMock->expects($this->any())
            ->method('getDataUsingMethod')
            ->willReturnCallback(
                function ($origName) {
                    return $origName;
                }
            );
        $attributeMock->expects($this->any())
            ->method('usesSource')
            ->willReturn(true);
        $attributeMock->expects($this->any())
            ->method('getSource')
            ->willReturn($sourceMock);

        $this->eavValidationRulesMock->expects($this->any())
            ->method('build')
            ->with($attributeMock, $this->logicalNot($this->isEmpty()));

        return [$attributeMock];
    }
}
