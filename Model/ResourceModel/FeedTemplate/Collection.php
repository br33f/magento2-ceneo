<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\ResourceModel\FeedTemplate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ceneo\Feed\Model\FeedTemplate;
use Ceneo\Feed\Model\ResourceModel\FeedTemplate as FeedTemplateResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = FeedTemplateResource::PRIMARY_KEY_NAME;

    /**
     * Function for initialize model
     */
    protected function _construct()
    {
        $this->_init(FeedTemplate::class, FeedTemplateResource::class);
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
