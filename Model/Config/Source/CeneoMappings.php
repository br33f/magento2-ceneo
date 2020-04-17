<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\Config\Source;

use Ceneo\Feed\Model\ResourceModel\FeedCategory\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class CeneoMappings implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    private $feedCategoryFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $feedCategoryFactory
     */
    public function __construct(
        CollectionFactory $feedCategoryFactory
    ) {
        $this->feedCategoryFactory = $feedCategoryFactory;
    }

    /**
     * Return option array
     *
     * @param bool $addEmpty
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['label' => __('-- Please Select Ceneo Category Mapping --'), 'value' => ''];
        $feedCategory = $this->feedCategoryFactory->create();

        foreach ($feedCategory as $value => $label) {
            $options[] = [
                'label' => $label->getName(),
                'value' => $label->getFeedCategoryId()
            ];
        }

        return $options;
    }
}
