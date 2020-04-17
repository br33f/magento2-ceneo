<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Model;

/**
 * Class FeedTest
 *
 * @package Ceneo\Feed\Test\Unit\Model
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class FeedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Ceneo\Feed\Model\Feed\Condition\Combine|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $combineMock;

    /**
     * @var \Ceneo\Feed\Model\Feed\Condition\CombineFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $condProdCombineF;

    /**
     * @var \Ceneo\Feed\Model\ResourceModel\ProductAdapter\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterMock;

    /**
     * @var \Ceneo\Feed\Model\Feed
     */
    protected $feedModel;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $contextMock = $this->createMock(\Magento\Framework\Model\Context::class);
        $registryMock = $this->createMock(\Magento\Framework\Registry::class);
        $formFactoryMock = $this->createMock(\Magento\Framework\Data\FormFactory::class);
        $localeDateMock = $this->createMock(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class);
        $combineFactoryMock = $this->getMockBuilder(\Ceneo\Feed\Model\Feed\Condition\CombineFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->combineMock = $this->getMockForAbstractClass(
            \Ceneo\Feed\Model\Feed\Condition\Combine::class,
            [],
            '',
            false,
            true,
            true,
            ['setRule', 'getConditions']
        );
        $couponMock = $this->createMock(\Magento\SalesRule\Model\Coupon::class);
        $couponMock
            ->method('setId')
            ->willReturn($this->createMock(\Magento\Framework\Model\AbstractModel::class));
        $this->combineMock
            ->method('setRule')
            ->willReturn($couponMock);
        $this->combineMock
            ->method('getConditions')
            ->willReturn([]);
        $combineFactoryMock
            ->method('create')
            ->willReturn($this->combineMock);
        $this->condProdCombineF = $this->getMockBuilder(\Ceneo\Feed\Model\Feed\Condition\CombineFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->condProdCombineF
            ->method('create')
            ->willReturn($this->combineMock);
        $productsCollectionFactoryMock = $this->getMockBuilder(\Ceneo\Feed\Model\ResourceModel\ProductAdapter\CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->adapterMock = $this->createMock(\Ceneo\Feed\Model\ResourceModel\ProductAdapter\Collection::class);
        $productsCollectionFactoryMock
            ->method('create')
            ->willReturn($this->adapterMock);
        $sqlBuilderMock = $this->createMock(\Magento\Rule\Model\Condition\Sql\Builder::class);
        $catalogProductVisibilityMock = $this->createMock(\Magento\Catalog\Model\Product\Visibility::class);
        $this->feedModel = $this->objectManager->getObject(
            \Ceneo\Feed\Model\Feed::class,
            [
                'context' => $contextMock,
                'registry' => $registryMock,
                'formFactory' => $formFactoryMock,
                'localeDate' => $localeDateMock,
                'combineFactory' => $combineFactoryMock,
                'condProdCombineF' => $this->condProdCombineF,
                'productsCollectionFactory' => $productsCollectionFactoryMock,
                'sqlBuilder' => $sqlBuilderMock,
                'catalogProductVisibility' => $catalogProductVisibilityMock,
            ]
        );
    }

    /**
     * Test function
     */
    public function testCreateProductCollection()
    {
        $this->assertEquals($this->adapterMock, $this->feedModel->createProductCollection());
    }

    /**
     * Test function
     */
    public function testGetConditionsInstance()
    {
        $this->assertEquals($this->combineMock, $this->feedModel->getConditionsInstance());
    }

    /**
     * Test function
     */
    public function testGetActionsInstance()
    {
        $this->assertEquals($this->combineMock, $this->feedModel->getActionsInstance());
    }

    /**
     * Test function
     */
    public function testGetIdentities()
    {
        $id = 1;
        $this->feedModel->setId($id);
        $this->assertEquals([$this->feedModel::CACHE_TAG . '_' . $id], $this->feedModel->getIdentities());
    }

    /**
     * Test function
     */
    public function testGetConditionsFieldSetId()
    {
        $id = 1;
        $this->feedModel->setId($id);
        $formName = 'test';
        $this->assertEquals($formName . 'feed_conditions_fieldset_' . $id,
            $this->feedModel->getConditionsFieldSetId($formName));
    }

    /**
     * Test function
     */
    public function testGetActionsFieldSetId()
    {
        $id = 1;
        $this->feedModel->setId($id);
        $formName = 'test';
        $this->assertEquals($formName . 'feed_actions_fieldset_' . $id,
            $this->feedModel->getActionsFieldSetId($formName));
    }

    /**
     * Test function
     */
    public function testGetAvailableStatuses()
    {
        $this->assertEquals([
            $this->feedModel::ENABLED => __('Enabled'),
            $this->feedModel::DISABLED => __('Disabled')
        ], $this->feedModel->getAvailableStatuses());
    }
}
