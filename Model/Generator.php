<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model;

use Ceneo\Feed\Block\Adminhtml\Feeds\FeedTemplate;
use Ceneo\Feed\Helper\Configuration;
use Ceneo\Feed\Model\MessageBroker\MessageBrokerInterface;
use Ceneo\Feed\Model\ResourceModel\Feed as FeedResource;
use Ceneo\Feed\Model\ResourceModel\Feed\Collection;
use Ceneo\Feed\Model\ResourceModel\Feed\CollectionFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Profiler;

class Generator
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var File
     */
    private $file;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var bool
     */
    private $force = false;

    /**
     * @var int
     */
    private $feedId = null;

    /**
     * @var int
     */
    private $generationDelay = 0;

    /**
     * @var
     */
    private $messageBrokers = [];

    /**
     * @var
     */
    private $content;
    /**
     * @var FeedTemplate
     */
    private $feedTemplate;

    /**
     * Generator constructor.
     *
     * @param Configuration $configuration
     * @param File $file
     * @param CollectionFactory $collectionFactory
     * @param FeedTemplate $feedTemplate
     */
    public function __construct(
        Configuration $configuration,
        File $file,
        CollectionFactory $collectionFactory,
        FeedTemplate $feedTemplate
    ) {
        $this->configuration = $configuration;
        $this->file = $file;
        $this->collectionFactory = $collectionFactory;
        $this->feedTemplate = $feedTemplate;
    }

    /**
     * @param MessageBrokerInterface $broker
     * @return $this
     */
    public function addMessageBroker(MessageBrokerInterface $broker)
    {
        $this->messageBrokers[] = $broker;
        return $this;
    }

    /**
     * @param $message
     * @param bool $success
     * @return $this
     */
    public function distributeMessage($message, $success = true)
    {
        foreach ($this->messageBrokers as $broker) {
            /* @var $broker MessageBrokerInterface */
            $broker->publishMessage('FEED GENERATOR ' . $message, $success);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isForce(): bool
    {
        return $this->force;
    }

    /**
     * @param bool $force
     */
    public function setForce($force)
    {
        $this->force = (bool) $force;
    }

    /**
     * @return int
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    /**
     * @param int $feedId
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;
    }

    /**
     * @return int
     */
    public function getGenerationDelay(): int
    {
        return $this->generationDelay;
    }

    /**
     * @param int $generationDelay
     */
    public function setGenerationDelay($generationDelay)
    {
        $this->generationDelay = (int) $generationDelay;
    }

    /**
     * @return bool
     */
    public function run()
    {
        $this->distributeMessage('Try to run generation');
        if (! $this->configuration->isModuleEnabled()) {
            $this->distributeMessage('Generation stopped. Module is disabled');
            return false;
        }

        $location = $this->configuration->getFeedsDirectory();
        if (! $this->file->isWriteable($location) && ! $this->file->mkdir($location)) {
            $this->distributeMessage("Generation stopped. Location {$location} is not writabled.");
            return false;
        }

        if (($profilerEnabled = Profiler::isEnabled())) {
            Profiler::disable();
        }

        /**
         * @var $feedsCollection Collection
         */
        $feedsCollection = $this->collectionFactory->create();

        if ($this->getFeedId()) {
            $this->distributeMessage("Adds feed id filter: {$this->getFeedId()}");
            $feedsCollection->addFieldToFilter(FeedResource::PRIMARY_KEY_NAME, $this->getFeedId());
        }
        if (! $this->isForce()) {
            $this->distributeMessage('Adds active feeds filter');
            $feedsCollection->applyActiveFilter();
        }

        $this->distributeMessage("Wait {$this->generationDelay} sec.");
        sleep($this->generationDelay);

        foreach ($feedsCollection as $feedModel) {
            $this->processFeedGeneration($feedModel);
        }

        $this->distributeMessage('Generation finished');

        if ($profilerEnabled) {
            Profiler::enable();
        }
    }

    /**
     * @param Feed $feed
     * @return bool
     */
    private function processFeedGeneration(Feed $feed)
    {
        $filePath = $this->configuration->getFeedPath($feed);
        $itemsCollection = $feed->createProductCollection();

        $this->distributeMessage("Generate {$itemsCollection->count()} products to {$filePath} file");
        $this->getContent($itemsCollection, $feed);

        if (! $this->file->write($filePath, $this->content)) {
            $this->distributeMessage("Unable to save file {$filePath}", false);
            return false;
        }

        $this->distributeMessage('File saved');

        return true;
    }

    /**
     * @param $collection
     * @param $feed
     * @return mixed
     */
    private function getContent($collection, $feed)
    {
        $this->feedTemplate->setTemplate('Ceneo_Feed::ceneo_feed.phtml');
        $this->feedTemplate->setFeedCollection($collection);
        $this->feedTemplate->setFeedData($feed);
        $this->content = $this->feedTemplate->toHtml();
        return $this->content;
    }
}
