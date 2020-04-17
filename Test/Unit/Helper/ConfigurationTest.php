<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Helper;

/**
 * Class ConfigurationTest
 *
 * @package Ceneo\Feed\Test\Unit\Helper
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Ceneo\Feed\Model\Feed|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $feedModelMock;

    /**
     * @var \Ceneo\Feed\Helper\Configuration
     */
    protected $helper;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->contextMock = $this->createMock(\Magento\Framework\App\Helper\Context::class);
        $this->storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $storeManagerInterfaceMock = $this->getMockForAbstractClass(
            \Magento\Store\Model\StoreManagerInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['getBaseUrl']
        );
        $this->storeManagerMock
            ->method('getStore')
            ->willReturn($storeManagerInterfaceMock);
        $this->filesystemMock = $this->createMock(\Magento\Framework\Filesystem::class);
        $this->filesystemMock
            ->method('getDirectoryWrite')
            ->willReturn($this->createMock(\Magento\Framework\Filesystem\Directory\WriteInterface::class));
        $this->contextMock
            ->method('getScopeConfig')
            ->willReturn($this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class));
        $this->feedModelMock = $this->createMock(\Ceneo\Feed\Model\Feed::class);
        $this->helper = $this->objectManager->getObject(
            \Ceneo\Feed\Helper\Configuration::class,
            [
                'context' => $this->contextMock,
                'storeManager' => $this->storeManagerMock,
                'filesystem' => $this->filesystemMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testIsModuleEnabled()
    {
        $this->assertEquals(null, $this->helper->isModuleEnabled());
    }

    /**
     * Test function
     */
    public function testIsCronEnabled()
    {
        $this->assertEquals(null, $this->helper->isCronEnabled());
    }

    /**
     * Test function
     */
    public function testUseRawImage()
    {
        $this->assertEquals(null, $this->helper->useRawImage());
    }

    /**
     * Test function
     */
    public function testGetExcludedCategoriesName()
    {
        $this->assertEquals(['Root Catalog', 'Default Category'], $this->helper->getExcludedCategoriesName());
    }

    /**
     * Test function
     */
    public function testGetMediaPublicDirectory()
    {
        $this->assertEquals(null, $this->helper->getMediaPublicDirectory());
    }

    /**
     * Test function
     */
    public function testGetFeedsPublicDirectory()
    {
        $this->assertEquals('//', $this->helper->getFeedsPublicDirectory());
    }

    /**
     * Test function
     */
    public function testGetFeedsDirectory()
    {
        $this->assertEquals('///', $this->helper->getFeedsDirectory());
    }

    /**
     * Test function
     */
    public function testGetFeedPath()
    {
        $this->assertEquals('//', $this->helper->getFeedPath($this->feedModelMock));
    }

    /**
     * Test function
     */
    public function testCreateFeedPath()
    {
        $filename = 'test';
        $this->assertEquals('//' . $filename, $this->helper->createFeedPath($filename));
    }

    /**
     * Test function
     */
    public function testGetStockMinQty()
    {
        $this->assertEquals(null, $this->helper->getStockMinQty());
    }

    /**
     * Test function
     */
    public function testGetBaseUrl()
    {
        $this->assertEquals(null, $this->helper->getBaseUrl());
    }
}
