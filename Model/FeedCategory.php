<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model;

use Ceneo\Feed\Model\ResourceModel\FeedCategory as ResourceFeedCategory;
use Magento\Framework\Model\AbstractModel;

class FeedCategory extends AbstractModel
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(ResourceFeedCategory::class);
    }

    /**
     * Return feed_category_id by given code
     *
     * @param int $feedCategoryCode
     * @return int | null
     */
    public function getFeedCategoryIdByCode($feedCategoryCode)
    {
        $feedCategory = $this->load($feedCategoryCode, 'code');

        return (!empty($feedCategory) ? $feedCategory->getFeedCategoryId() : null);
    }
}
