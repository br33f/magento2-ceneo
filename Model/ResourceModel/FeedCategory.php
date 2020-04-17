<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FeedCategory extends AbstractDb
{
    const TABLE_NAME = 'catalog_product_feed_category';
    const PRIMARY_KEY_NAME = 'feed_category_id';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY_NAME);
    }
}
