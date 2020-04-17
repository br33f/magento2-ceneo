<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Helper;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\App\Helper\AbstractHelper;
use Ceneo\Feed\Model\ResourceModel\FeedCategory;
use Ceneo\Feed\Model\ResourceModel\FeedCategoryMapping\Collection as FeedCategoryMappingCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Ceneo\Feed\Model\FeedCategoryFactory;

class Data extends AbstractHelper
{
    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;

    /**
     * @var FeedCategoryMappingCollection
     */
    protected $feedCategoryMappingCollection;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var FeedCategoryFactory
     */
    protected $feedCategoryFactory;

    /**
     * Helper Construction
     *
     * @param Context $context
     * @param CategoryHelper $categoryHelper
     * @param FeedCategoryMappingCollection $feedCategoryMappingCollection
     * @param StoreManagerInterface $storeManager
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param FeedCategoryFactory $feedCategoryFactory
     */
    public function __construct(
        Context $context,
        CategoryHelper $categoryHelper,
        FeedCategoryMappingCollection $feedCategoryMappingCollection,
        StoreManagerInterface $storeManager,
        CategoryCollectionFactory $categoryCollectionFactory,
        FeedCategoryFactory $feedCategoryFactory
    ) {
        $this->categoryHelper = $categoryHelper;
        $this->feedCategoryMappingCollection = $feedCategoryMappingCollection;
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->feedCategoryFactory = $feedCategoryFactory;
        parent::__construct($context);
    }

    /**
     * Return category list array. If $mappedData is given, function merge two arrays
     *
     * @param array | boolean $mappedData
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCategoryListArray($mappedData = false)
    {
        $categories = $this->getStoreCategories();
        $result = [];
        foreach ($categories as $category) {
            $categoryId = $category->getEntityId();

            if ($mappedData && isset($mappedData[$category->getEntityId()])) {
                $result[$category->getEntityId()] = [
                    'name' => $category->getName(),
                    'mapped_name' => $mappedData[$category->getEntityId()],
                    'level' => (int) $category->getLevel()
                ];
            } else {
                $result[$category->getEntityId()] = [
                    'name' => $category->getName(),
                    'level' => (int) $category->getLevel()
                ];
            }
        }
        return $result;
    }

    /**
     * Exclude selected Categories
     *
     * @param array $categoriesArray
     * @param int $feedCategoryId
     * @return array | null
     */
    public function excludeSelectedCategories($categoriesArray, $feedCategoryId)
    {
        $feedCategory = $this->feedCategoryFactory->create()->load($feedCategoryId);
        $excludedCategories = explode(',', $feedCategory->getExcludeCategories());

        if (empty($excludedCategories)) {
            return $categoriesArray;
        }
        $excludedCategories[] = 1;
        $excludedCategories[] = 2;

        return $this->arrayRemoveKeys($categoriesArray, $excludedCategories);
    }

    /**
     * Function removes from array keys from $keys array
     *
     * @param array $array
     * @param array $keys
     * @return array
     */
    public function arrayRemoveKeys($array, $keys = [])
    {

        if (empty($array) || (!is_array($array))) {
            return $array;
        }

        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }

        if (!is_array($keys)) {
            return $array;
        }

        $assocKeys = array();
        foreach ($keys as $key) {
            $assocKeys[$key] = true;
        }

        return array_diff_key($array, $assocKeys);
    }

    /**
     * Get current categories in store
     *
     * @return Collection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getStoreCategories()
    {
        return $this->categoryCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToSort('path')
                ->setStore($this->storeManager->getStore());
    }

    /**
     * Function prepare given array for multiple insert function
     *
     * @param array $mappedCategories
     * @return array
     */
    public function formatInsertArray($mappedCategories)
    {
        $return = [];

        foreach ($mappedCategories as $mappedCategory) {
            $mappedCategory['value'] = isset($mappedCategory['value']) ? $mappedCategory['value'] : null;

            $return[] = [
                FeedCategory::PRIMARY_KEY_NAME => $mappedCategory[FeedCategory::PRIMARY_KEY_NAME],
                'category_id' => $mappedCategory['category_id'],
                'value' => $mappedCategory['value']
            ];
        }

        return $return;
    }

    /**
     * Return mapped feed categories by given feed_category_id
     *
     * @param int $feedCategoryId
     * @return array
     */
    public function getMappedCategories($feedCategoryId)
    {
        $feedCategoryMapping = $this->feedCategoryMappingCollection->getCollectionByFeedCategoryId($feedCategoryId);
        $result = [];

        foreach ($feedCategoryMapping as $category) {
            $result[$category->getCategoryId()] = $category->getValue();
        }

        return $result;
    }
}
