<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\ResourceModel;

use Magento\Rule\Model\ResourceModel\AbstractResource;

class Feed extends AbstractResource
{
    /**
     * Module table name
     */
    const TABLE_NAME = 'catalog_product_feed';

    /**
     * Module primary key
     */
    const PRIMARY_KEY_NAME = 'id';

    /**
     * Function for initialize model
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::PRIMARY_KEY_NAME);
    }
}
