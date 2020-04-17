<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model;

use Ceneo\Feed\Model\Feed\Condition\Combine;
use Ceneo\Feed\Model\Feed\Condition\CombineFactory;
use Ceneo\Feed\Model\ResourceModel\ProductAdapter\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Magento\Rule\Model\Condition\Sql\Builder;

class Feed extends AbstractModel
{
    /**
     * Enable value
     */
    const ENABLED = '1';

    /**
     * Disable value
     */
    const DISABLED = '0';

    /**
     * Cache tag
     */
    const CACHE_TAG = 'aurora_automaticrelatedproducts_rule';

    /**
     * @var ResourceModel\ProductAdapter\CollectionFactory
     */
    private $productsCollectionFactory;

    /**
     * @var Visibility
     */
    private $catalogProductVisibility;

    /**
     * @var Builder
     */
    private $sqlBuilder;

    /**
     * @inheritdoc
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $combineFactory,
        CombineFactory $condProdCombineF,
        CollectionFactory $productsCollectionFactory,
        Builder $sqlBuilder,
        Visibility $catalogProductVisibility,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        ExtensionAttributesFactory $extensionFactory = null,
        AttributeValueFactory $customAttributeFactory = null,
        Json $serializer = null
    ) {
        $this->combineFactory = $combineFactory;
        $this->condProdCombineF = $condProdCombineF;
        $this->productsCollectionFactory = $productsCollectionFactory;
        $this->sqlBuilder = $sqlBuilder;
        $this->catalogProductVisibility = $catalogProductVisibility;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data,
            $extensionFactory,
            $customAttributeFactory,
            $serializer
        );
    }

    /**
     * Function for initialize model
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Feed::class);
    }

    /**
     * Prepare and return product collection
     *
     * @return Collection
     * @throws LocalizedException
     */
    public function createProductCollection()
    {
        $storeId = $this->getData('store_id');

        /** @var $collection Collection */
        $collection = $this->productsCollectionFactory->create();
        $collection->setStoreId($storeId);

        $this->getConditions()->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $this->getConditions());

        $collection->applyOnlyVisibileFilter();

        $collection->joinField(
            'qty',
            'cataloginventory_stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );

        $collection->addAttributeToSelect('*');
        $collection->addFinalPrice();
        $collection->distinct(true);
        $collection->addTierPriceData();

        return $collection;
    }

    /**
     * Getter for rule conditions collection
     *
     * @return Combine
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * Getter for rule conditions collection
     *
     * @return Combine
     */
    public function getActionsInstance()
    {
        return $this->condProdCombineF->create();
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'feed_conditions_fieldset_' . $this->getId();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getActionsFieldSetId($formName = '')
    {
        return $formName . 'feed_actions_fieldset_' . $this->getId();
    }

    /**
     * Prepare rule's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled')
        ];
    }
}
