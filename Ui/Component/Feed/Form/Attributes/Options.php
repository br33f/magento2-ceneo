<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Ui\Component\Feed\Form\Attributes;

use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
use Magento\Framework\Option\ArrayInterface;

class Options implements ArrayInterface
{
    /**
     * @var Collection
     */
    private $collection;

    public $options;

    public function __construct(
        Collection $collection
    ) {
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $collection = $this->collection->addFieldToFilter(Set::KEY_ENTITY_TYPE_ID, 4)->load()->getItems();
            $array = [];
            foreach ($collection as $item) {
                $array[] = [
                    'value' => $item->getData()['attribute_id'],
                    'label' => $item->getData()['frontend_label']
                ];
            }
            $this->options = $array;
        }

        return $this->options;
    }

}
