<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Block\Adminhtml\Feeds;

class ConditionsTest extends \PHPUnit\Framework\TestCase
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
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\Data\FormFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formFactoryMock;

    /**
     * @var \Magento\Rule\Block\Conditions|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $conditionsMock;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldsetMock;

    /**
     * @var \Ceneo\Feed\Block\Adminhtml\Feeds\Conditions
     */
    protected $conditionsObject;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->contextMock = $this->createMock(\Magento\Backend\Block\Template\Context::class);
        $this->registryMock = $this->createMock(\Magento\Framework\Registry::class);
        $this->formFactoryMock = $this->createMock(\Magento\Framework\Data\FormFactory::class);
        $this->conditionsMock = $this->createMock(\Magento\Rule\Block\Conditions::class);
        $this->fieldsetMock = $this->createMock(\Magento\Backend\Block\Widget\Form\Renderer\Fieldset::class);
        $this->conditionsObject = $this->objectManager->getObject(
            \Ceneo\Feed\Block\Adminhtml\Feeds\Conditions::class,
            [
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'formFactory' => $this->formFactoryMock,
                'conditions' => $this->conditionsMock,
                'rendererFieldset' => $this->fieldsetMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testGetTabLabel()
    {
        $this->assertEquals(__('Conditions'), $this->conditionsObject->getTabLabel());
    }

    /**
     * Test function
     */
    public function testGetTabTitle()
    {
        $this->assertEquals(__('Conditions'), $this->conditionsObject->getTabTitle());
    }

    /**
     * Test function
     */
    public function testCanShowTab()
    {
        $this->assertEquals(true, $this->conditionsObject->canShowTab());
    }

    /**
     * Test function
     */
    public function testIsHidden()
    {
        $this->assertEquals(false, $this->conditionsObject->isHidden());
    }

    /**
     * Test function
     */
    public function testGetTabClass()
    {
        $this->assertEquals(null, $this->conditionsObject->getTabClass());
    }

    /**
     * Test function
     */
    public function testGetTabUrl()
    {
        $this->assertEquals(null, $this->conditionsObject->getTabUrl());
    }

    /**
     * Test function
     */
    public function testIsAjaxLoaded()
    {
        $this->assertEquals(false, $this->conditionsObject->isAjaxLoaded());
    }
}
