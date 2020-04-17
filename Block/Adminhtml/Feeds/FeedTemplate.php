<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Block\Adminhtml\Feeds;

use Ceneo\Feed\Helper\Configuration;
use Ceneo\Feed\Model\Feed;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class FeedTemplate extends Template
{
    /**
     * @var Feed
     */
    private $feed;

    /**
     * @var Config
     */
    private $eavConfig;

    public $feedCollection;
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * FeedTemplate constructor.
     *
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param Configuration $configuration
     * @param Feed $feed
     * @param Config $eavConfig
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ProductAttributeRepositoryInterface $productAttributeRepository,
        Configuration $configuration,
        Feed $feed,
        Config $eavConfig,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->feed = $feed;
        $this->eavConfig = $eavConfig;
        $this->configuration = $configuration;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * @return Collection
     * @throws LocalizedException
     */
    public function getFeedProductCollection()
    {
        if ($this->feedCollection === null) {
            $this->feedCollection = $this->feed->createProductCollection();
        }
        return $this->feedCollection;
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function setFeedCollection($collection)
    {
        $this->feedCollection = $collection;
        return $this->feedCollection;
    }

    /**
     * @param $product
     * @return bool|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getManufacturer($product)
    {
        $attributeId = $this->configuration->getManufacturerAttribute();
        $attribute = $this->productAttributeRepository->get($attributeId);
        $attributeCode = $attribute->getAttributeCode();

        if ($attribute->usesSource()) {
            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeId);
            return $attribute->getSource()->getOptionText($product->getData($attributeCode));
        } else {
            return $product->getData($attributeCode);
        }
    }

    /**
     * @param $product
     * @return bool|string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getManufacturerCode($product)
    {
        $attributeId = $this->configuration->getManufacturerCodeAttribute();
        $attribute = $this->productAttributeRepository->get($attributeId);
        $attributeCode = $attribute->getAttributeCode();

        if ($attribute->usesSource()) {
            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeId);
            return $attribute->getSource()->getOptionText($product->getData($attributeCode));
        } else {
            return $product->getData($attributeCode);
        }
    }

    /**
     * @param $product
     * @return string
     */
    public function getProductWeight($product)
    {
        if ($product->getWeight() > 0) {
            return 'weight="' . $product->getWeight() . '"';
        }
        return '';
    }

    /**
     * @param $product
     * @return string
     */
    public function getProductStock($product)
    {
        if ($product->getQty() > 0) {
            return 'stock="' . $product->getQty() . '"';
        }
        return '';
    }

    /**
     * @param $product
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getEan($product)
    {
        $attributeId = $this->configuration->getEanAttribute();
        $attributeCode = $this->productAttributeRepository->get($attributeId)->getAttributeCode();
        return $product->getData($attributeCode);
    }

    /**
     * @param $product
     * @return string
     */
    public function getBasket($product)
    {
        $buyAtCeneo = $product->getBuyAtCeneo();
        return (($buyAtCeneo || is_null($buyAtCeneo)) ? 'basket="1"' : 'basket="0"');
    }

    /**
     * @param $feed
     * @return array|null
     * @throws LocalizedException
     */
    public function getAdditionalAttributes($feed)
    {
        $additionalAttributes = $feed->getAdditionalAttributes();

        if (empty($additionalAttributes)) {
            return null;
        }

        $explodedAttributes = explode(',', $additionalAttributes);

        if (empty($explodedAttributes)) {
            return null;
        }

        $return = [];
        foreach ($explodedAttributes as $attributeId) {
            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeId);
            $check = $attribute->usesSource();
            $return[$attributeId] = [
                'attribute_id' => $attributeId,
                'label' => $attribute->getFrontendLabel(),
                'code' => $attribute->getAttributeCode(),
                'multiple' => $check
            ];
        }

        return $return;
    }

    /**
     * Retrieve attribute option label
     *
     * @param $attributeId
     * @param $optionId
     * @return bool|string
     * @throws LocalizedException
     */
    public function getOptionLabel($attributeId, $optionId)
    {
        $optionsIds = explode(',', $optionId);
        $resultArray = [];
        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeId);
        foreach ($optionsIds as $optionId) {
            $resultArray[] = $attribute->getSource()->getOptionText($optionId);
        }

        return implode(', ', $resultArray);
    }
}
