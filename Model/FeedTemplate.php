<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model;

use Magento\Framework\Model\AbstractModel;

class FeedTemplate extends AbstractModel
{
    /**
     * Function for initialize model
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\FeedTemplate::class);
    }
}
