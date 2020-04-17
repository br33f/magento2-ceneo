<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Console\Command;

use Ceneo\Feed\Helper\Configuration;
use Ceneo\Feed\Model\Generator;
use Ceneo\Feed\Model\MessageBroker\MessageBrokerInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FeedGenerationCommand extends Command implements MessageBrokerInterface
{
    /**
     * Ignore configuration and force generation
     */
    const OPT_FORCE = 'force';

    /**
     * Ignore configuration and force generation
     */
    const OPT_FORCE_SHORT = 'f';

    /**
     * Feed id
     */
    const OPT_FEED = 'feed';

    /**
     * Feed id
     */
    const OPT_FEED_SHORT = 'i';

    /**
     * Enable verbose mode
     */
    const OPT_DEBUG = 'debug';

    /**
     * Enable verbose mode
     */
    const OPT_DEBUG_SHORT = 'd';

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var State
     */
    private $state;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var boolean
     */
    private $debug = false;

    /**
     * FeedGenerationCommand constructor.
     * @param State $state
     * @param Configuration $configuration
     * @param Generator $generator
     * @param null $name
     */
    public function __construct(
        State $state,
        Configuration $configuration,
        Generator $generator,
        $name = null
    ) {
        $this->state = $state;
        $this->configuration = $configuration;
        $this->generator = $generator;
        $this->generator->addMessageBroker($this);
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('catalog:product:feed:generate')
            ->setDescription('Generate products feeds');

        $this->addOption(
            self::OPT_FEED,
            self::OPT_FEED_SHORT,
            InputOption::VALUE_REQUIRED,
            'Feed id'
        );
        $this->addOption(
            self::OPT_FORCE,
            self::OPT_FORCE_SHORT,
            InputOption::VALUE_NONE,
            'Force'
        );
        $this->addOption(
            self::OPT_DEBUG,
            self::OPT_DEBUG_SHORT,
            InputOption::VALUE_NONE,
            'Debug'
        );

        parent::configure();
    }

    /**
     * Run the tests
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int Non zero if invalid type, 0 otherwise
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        if (! $this->configuration->isModuleEnabled()) {
            return;
        }

        try {
            $force = $input->getOption(self::OPT_FORCE);
            $feedId = $input->getOption(self::OPT_FEED);
            $this->debug = $input->getOption(self::OPT_DEBUG);

            $this->state->setAreaCode(Area::AREA_ADMINHTML);

            $this->generator->setFeedId($feedId);
            $this->generator->setForce($force);
            $this->generator->setGenerationDelay(1);
            $this->generator->run();

            return Cli::RETURN_SUCCESS;
        } catch (LocalizedException $e) {
            $this->generator->distributeMessage("Error: " . $e->getMessage(), false);
            return Cli::RETURN_FAILURE;
        }
    }

    /**
     * @param $message
     * @param bool $success
     * @return $this
     */
    public function publishMessage($message, $success = true)
    {
        if (! $this->debug) {
            return $this;
        } elseif ($success) {
            $this->output->writeln($message);
        } else {
            $this->output->writeln($message);
        }

        return $this;
    }
}
