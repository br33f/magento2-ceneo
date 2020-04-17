<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\ResourceModel\ProductAdapter;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Ceneo\Feed\Model\ProductAdapter;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Function for initialize model
     */
    protected function _construct()
    {
        $this->_init(ProductAdapter::class, ProductResource::class);
    }

    /**
     * @param array $types
     * @return $this
     */
    public function applyTypesFilter(array $types)
    {
        $this->addFieldToFilter('type_id', [
            'in' => $types
        ]);
        return $this;
    }

    /**
     * @return $this
     */
    public function applyOnlyVisibileFilter()
    {
        $this->addFieldToFilter('visibility', [
            'neq' => Visibility::VISIBILITY_NOT_VISIBLE
        ]);
        return $this;
    }

    /**
     * @param array $ids
     * @return $this
     */
    public function applyIdsFilter(array $ids)
    {
        $this->addAttributeToFilter('entity_id', [
            'in' => $ids
        ]);
        return $this;
    }
}
