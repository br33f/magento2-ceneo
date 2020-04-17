<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Ui;

use Ceneo\Feed\Helper\Data as Helper;
use Ceneo\Feed\Model\ResourceModel\FeedCategory;
use Ceneo\Feed\Model\ResourceModel\FeedCategory\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\AbstractDataProvider;

class FeedCategoryFormDataProvider extends AbstractDataProvider
{
    const CATEGORY_LEVEL_LIMIT = 2;

    /**
     * @var  CollectionFactory
     */
    protected $collection;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * FeedCategoryFormDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Helper $helper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        $collectionFactory,
        Helper $helper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->helper = $helper;
    }

    /**
     * Transform data sent to UI component
     *
     * @return array
     */
    public function getData()
    {
        $result = [];

        foreach ($this->collection->getItems() as $item) {
            $result[$item->getId()] = $item->getData();
            $result[$item->getId()]['exclude_categories'] = explode(',', $item->getExcludeCategories());
            $mappedData = $this->helper->getMappedCategories($item->getId());
            $categories = $this->helper->getCategoryListArray($mappedData);
            $correctArray = $this->helper->excludeSelectedCategories($categories, $item->getId());

            foreach ($correctArray as $categoryId => $category) {
                $level = $category['level'];

                if ($level > self::CATEGORY_LEVEL_LIMIT) {
                    $name = str_repeat('---', $level) . " {$category['name']}";
                } else {
                    $name = $category['name'];
                }

                $result[$item->getId()]['mapped_categories_container'][] = [
                    FeedCategory::PRIMARY_KEY_NAME => $item->getId(),
                    'category_id' => $categoryId,
                    'category_name' => $name,
                    'value' => (isset($category['mapped_name']) ? $category['mapped_name'] : ''),
                ];
            }
        }
        return $result;
    }
}
