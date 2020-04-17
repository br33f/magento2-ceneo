<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\ResourceModel\CeneoCategory;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Ceneo\Feed\Model\CeneoCategory;
use Ceneo\Feed\Model\ResourceModel\CeneoCategory as CeneoCategoryResource;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = CeneoCategoryResource::PRIMARY_KEY_NAME;

    /**
     * Function for initialize model
     */
    protected function _construct()
    {
        $this->_init(CeneoCategory::class, CeneoCategoryResource::class);
    }
}
