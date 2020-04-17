<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\Config\Source;

use Ceneo\Feed\Model\FeedTemplate;
use Ceneo\Feed\Model\ResourceModel\FeedTemplate\Collection;
use Ceneo\Feed\Model\ResourceModel\FeedTemplate\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class FeedTemplates implements ArrayInterface
{
    /**
     * @var array
     */
    private $templatesCached;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Return option array
     *
     * @param bool $addEmpty
     * @return array
     */
    public function toOptionArray($addEmpty = true)
    {
        $this->init();

        if ($addEmpty) {
            return array_merge([[
                'label' => __('-- Select template to load --'),
                'value' => ''
            ]], $this->templatesCached);
        }

        return $this->templatesCached;
    }

    /**
     * Function for initialize data
     *
     * @param bool $refresh
     */
    public function init($refresh = false)
    {
        if (! $refresh && is_array($this->templatesCached)) {
            return;
        }

        /**
         * @var $collection Collection
         */
        $collection = $this->collectionFactory->create();
        $collection->applyActiveFilter();

        if ($collection->count() < 1) {
            return;
        }

        $this->templateCollectionFactory = [];
        $items = $collection->getItems();
        foreach ($items as $feedTemplate) {
            /* @var $feedTemplate FeedTemplate */
            $this->templatesCached[] = [
                'label' => $feedTemplate->getName(),
                'value' => $feedTemplate->getId(),
            ];
        }
    }
}
