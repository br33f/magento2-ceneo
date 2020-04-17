<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Cron;

use Ceneo\Feed\Helper\Configuration;
use Ceneo\Feed\Model\Generator;
use Ceneo\Feed\Model\MessageBroker\MessageBrokerInterface;
use Ceneo\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManager;
use Psr\Log\LoggerInterface;

class FeedGeneration implements MessageBrokerInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * FeedGeneration constructor.
     *
     * @param Configuration $configuration
     * @param Generator $generator
     * @param LoggerInterface $logger
     * @param CollectionFactory $collectionFactory
     * @param DateTime $date
     * @param StoreManager $storeManager
     */
    public function __construct(
        Configuration $configuration,
        Generator $generator,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory,
        DateTime $date,
        StoreManager $storeManager
    ) {
        $this->configuration = $configuration;
        $this->generator = $generator;
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->generator->addMessageBroker($this);
    }

    /**
     * Run
     */
    public function execute()
    {
        if (!$this->configuration->isModuleEnabled()) {
            $this->generator->distributeMessage('Module is disabled');
            return;
        } elseif (!$this->configuration->isCronEnabled()) {
            $this->generator->distributeMessage('Cron is disabled');
            return;
        }

        $this->storeManager->setCurrentStore($this->storeManager->getDefaultStoreView()->getId());

        $this->generator->distributeMessage('Cron fired');

        $feedsCollection = $this->collectionFactory->create();
        $feedsCollection->applyActiveFilter();

        foreach ($feedsCollection as $feedModel) {
            $cronExpression = $feedModel->getCronExpression();
            $path = $this->configuration->createFeedPath($feedModel->getFilename());
            if (empty($cronExpression)) {
                $this->generator->distributeMessage('No cron expression defined');
                continue;
            } elseif (file_exists($path) && !is_readable($path)) {
                $this->generator->distributeMessage('Broken file');
                continue;
            } elseif (file_exists($path)) {
                $lastGeneration = $this->date->date('Y-m-d H:i:s', filemtime($path));
                $cron = \Cron\CronExpression::factory($cronExpression);
                $nextGenerationTime = $cron->getNextRunDate($lastGeneration)->format('Y-m-d H:i:s');
                if (strtotime($nextGenerationTime) > time()) {
                    $this->generator->distributeMessage(
                        "Feed {$feedModel->getId()} skipped (next generation at time {$nextGenerationTime})"
                    );
                    continue;
                }
            }

            $this->generator->setFeedId($feedModel->getId());
            $this->generator->run();
        }

        return;
    }

    /**
     * Function for message publish
     *
     * @param $message
     * @param bool $success
     * @return $this
     */
    public function publishMessage($message, $success = true)
    {
        if ($success) {
            $this->logger->info($message);
        } else {
            $this->logger->error($message);
        }

        return $this;
    }
}
