<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CeneoCategory extends AbstractDb
{
    const TABLE_NAME = 'catalog_product_feed_ceneo_category';
    const PRIMARY_KEY_NAME = 'ceneo_category_id';

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY_NAME);
    }
}
