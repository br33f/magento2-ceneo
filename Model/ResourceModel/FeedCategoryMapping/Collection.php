<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\ResourceModel\FeedCategoryMapping;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ceneo\Feed\Model\FeedCategoryMapping;
use Ceneo\Feed\Model\ResourceModel\FeedCategory;
use Ceneo\Feed\Model\ResourceModel\FeedCategoryMapping as FeedCategoryMappingResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = FeedCategoryMappingResource::PRIMARY_KEY_NAME;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(FeedCategoryMapping::class, FeedCategoryMappingResource::class);
    }

    /**
     * Get feed category mapping collection by given feed_category_id
     *
     * @param int $feedCategoryId
     * @return $this
     */
    public function getCollectionByFeedCategoryId($feedCategoryId)
    {
        $this->addFilter(FeedCategory::PRIMARY_KEY_NAME, $feedCategoryId);
        return $this;
    }
}
