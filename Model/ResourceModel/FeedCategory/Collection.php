<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\ResourceModel\FeedCategory;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ceneo\Feed\Model\FeedCategory;
use Ceneo\Feed\Model\ResourceModel\FeedCategory as FeedCategoryResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = FeedCategoryResource::PRIMARY_KEY_NAME;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(FeedCategory::class, FeedCategoryResource::class);
    }
}
