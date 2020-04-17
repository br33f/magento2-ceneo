<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Console\Command;

use Ceneo\Feed\Console\Command\FeedGenerationCommand;

/**
 * Class FeedGenerationCommandTest
 *
 * @package Ceneo\Feed\Test\Unit\Console\Command
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class FeedGenerationCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var FeedGenerationCommand
     */
    protected $feedGenerationCommand;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->feedGenerationCommand = $this->objectManager->getObject(\Ceneo\Feed\Console\Command\FeedGenerationCommand::class);
    }

    /**
     * Test function
     */
    public function testPublishMessage()
    {
        $this->assertEquals($this->feedGenerationCommand, $this->feedGenerationCommand->publishMessage('test'));
    }
}
