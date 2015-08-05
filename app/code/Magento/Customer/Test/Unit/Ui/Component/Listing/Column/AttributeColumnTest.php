<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Test\Unit\Ui\Component\Listing\Column;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Ui\Component\Listing\Column\AttributeColumn;

class AttributeColumnTest extends \PHPUnit_Framework_TestCase
{
    /** @var AttributeColumn */
    protected $component;

    /** @var \Magento\Framework\View\Element\UiComponent\ContextInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\Framework\View\Element\UiComponentFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $uiComponentFactory;

    /** @var \Magento\Customer\Ui\Component\Listing\AttributeRepository|\PHPUnit_Framework_MockObject_MockObject */
    protected $attributeRepository;

    /** @var \Magento\Customer\Api\Data\AttributeMetadataInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $attributeMetadata;

    /** @var \Magento\Customer\Api\Data\OptionInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $genderOption;

    public function setup()
    {
        $this->context = $this->getMockForAbstractClass(
            'Magento\Framework\View\Element\UiComponent\ContextInterface',
            [],
            '',
            false
        );
        $this->uiComponentFactory = $this->getMock(
            'Magento\Framework\View\Element\UiComponentFactory',
            [],
            [],
            '',
            false
        );
        $this->attributeRepository = $this->getMock(
            'Magento\Customer\Ui\Component\Listing\AttributeRepository',
            [],
            [],
            '',
            false
        );
        $this->attributeMetadata = $this->getMockForAbstractClass(
            '\Magento\Customer\Api\Data\AttributeMetadataInterface',
            [],
            '',
            false
        );
        $this->genderOption = $this->getMockForAbstractClass(
            'Magento\Customer\Api\Data\OptionInterface',
            [],
            '',
            false
        );

        $this->component = new AttributeColumn(
            $this->context,
            $this->uiComponentFactory,
            $this->attributeRepository
        );
        $this->component->setData('name', 'gender');
    }

    public function testPrepareDataSourceWithoutItems()
    {
        $dataSource = [
            'data' => [

            ]
        ];
        $this->attributeRepository->expects($this->never())
            ->method('getMetadataByCode');

        $this->component->prepareDataSource($dataSource);
    }

    public function testPrepareDataSource()
    {
        $genderOptionId = 1;
        $genderOptionLabel = 'Male';

        $dataSource = [
            'data' => [
                'items' => [
                    [
                        'name' => 'testName'
                    ],
                    [
                        'gender' => $genderOptionId
                    ]
                ]
            ]
        ];
        $expectedSource = [
            'data' => [
                'items' => [
                    [
                        'name' => 'testName'
                    ],
                    [
                        'gender' => $genderOptionLabel
                    ]
                ]
            ]
        ];

        $this->attributeRepository->expects($this->once())
            ->method('getMetadataByCode')
            ->with('gender')
            ->willReturn($this->attributeMetadata);
        $this->attributeMetadata->expects($this->atLeastOnce())
            ->method('getOptions')
            ->willReturn([$this->genderOption]);
        $this->genderOption->expects($this->once())
            ->method('getValue')
            ->willReturn(1);
        $this->genderOption->expects($this->once())
            ->method('getValue')
            ->willReturn($genderOptionId);
        $this->genderOption->expects($this->once())
            ->method('getLabel')
            ->willReturn($genderOptionLabel);

        $this->component->prepareDataSource($dataSource);

        $this->assertEquals($expectedSource, $dataSource);
    }
}
