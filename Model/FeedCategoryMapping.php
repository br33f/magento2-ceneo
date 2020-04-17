<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model;

use Magento\Framework\Model\AbstractModel;

class FeedCategoryMapping extends AbstractModel
{

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\FeedCategoryMapping::class);
    }
}
