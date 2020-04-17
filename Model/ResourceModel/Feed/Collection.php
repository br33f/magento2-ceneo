<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\ResourceModel\Feed;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ceneo\Feed\Model\Feed;
use Ceneo\Feed\Model\ResourceModel\Feed as FeedResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = FeedResource::PRIMARY_KEY_NAME;

    /**
     * Function for initialize model
     */
    protected function _construct()
    {
        $this->_init(Feed::class, FeedResource::class);
    }

    /**
     * @return $this
     */
    public function applyActiveFilter()
    {
        $this->addFilter('enable', true);
        return $this;
    }
}
