<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Block\Adminhtml;

/**
 * Class BaseUrlTest
 *
 * @package Ceneo\Feed\Test\Unit\Block\Adminhtml
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class BaseUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Backend\Block\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\App\DeploymentConfig\Reader|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $readerMock;

    /**
     * @var \Ceneo\Feed\Block\Adminhtml\BaseUrl
     */
    protected $baseUrl;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->contextMock = $this->createMock(\Magento\Backend\Block\Template\Context::class);
        $this->contextMock
            ->method('getUrlBuilder')
            ->willReturn($this->createMock(\Magento\Framework\UrlInterface::class));
        $this->readerMock = $this->createMock(\Magento\Framework\App\DeploymentConfig\Reader::class);
        $this->readerMock->method('load')->willReturn([
            'backend' => [
                'frontName' => 'test-front-name'
            ]
        ]);
        $this->baseUrl = $this->objectManager->getObject(
            \Ceneo\Feed\Block\Adminhtml\BaseUrl::class,
            [
                'context' => $this->contextMock,
                'reader' => $this->readerMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testGetAdminBaseUrl()
    {
        $this->readerMock
            ->expects($this->once())
            ->method('load');
        $this->assertEquals('test-front-name/', $this->baseUrl->getAdminBaseUrl());
    }
}
