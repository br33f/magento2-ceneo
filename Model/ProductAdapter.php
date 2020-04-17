<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model;

use Ceneo\Feed\Helper\Configuration;
use Ceneo\Feed\Helper\Data as Helper;
use Ceneo\Feed\Model\ResourceModel\FeedCategoryMapping\CollectionFactory as FeedCategoryMappingCollectionFactory;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\ProductLinkExtensionFactory;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Indexer\Product\Eav\Processor as EavProcessor;
use Magento\Catalog\Model\Indexer\Product\Flat\Processor;
use Magento\Catalog\Model\Indexer\Product\Price\Processor as PriceProcessor;
use Magento\Catalog\Model\Product\Attribute\Backend\Media\EntryConverterPool;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Configuration\Item\OptionFactory;
use Magento\Catalog\Model\Product\Image\CacheFactory;
use Magento\Catalog\Model\Product\Link;
use Magento\Catalog\Model\Product\LinkTypeProvider;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Catalog\Model\Product\OptionFactory as ProductOptionFactory;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Url;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductLink\CollectionProvider;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Model\Context;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

class ProductAdapter extends \Magento\Catalog\Model\Product
{
    const PATHS_SEPARATOR = ',';

    const CATEGORIES_SEPARATOR = '/';

    /**
     * @var array
     */
    private static $prefixes = ['get', 'set', 'uns', 'has'];

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ImageBuilder
     */
    private $imageBuilder;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @var StockItemRepository
     */
    private $stockItemRepository;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var Configurable
     */
    private $catalogProductTypeConfigurable;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var FeedCategoryFactory
     */
    private $feedCategoryFactory;

    /**
     * @var Helper
     */
    private $feedHelper;

    /**
     * @var array
     */
    private static $parentUrlsCached = [];

    /**
     * @var ResourceModel\CeneoCategory\CollectionFactory
     */
    private $ceneoCollectionFactory;
    /**
     * @var FeedCategoryMappingFactory
     */
    private $feedCategoryMappingFactory;

    /**
     * ProductAdapter constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductAttributeRepositoryInterface $metadataService
     * @param Url $url
     * @param Link $productLink
     * @param OptionFactory $itemOptionFactory
     * @param StockItemInterfaceFactory $stockItemFactory
     * @param ProductOptionFactory $catalogProductOptionFactory
     * @param Visibility $catalogProductVisibility
     * @param Status $catalogProductStatus
     * @param Config $catalogProductMediaConfig
     * @param Type $catalogProductType
     * @param Manager $moduleManager
     * @param Product $catalogProduct
     * @param ProductResourceModel $resource
     * @param Collection $resourceCollection
     * @param CollectionFactory $collectionFactory
     * @param Filesystem $filesystem
     * @param IndexerRegistry $indexerRegistry
     * @param Processor $productFlatIndexerProcessor
     * @param PriceProcessor $productPriceIndexerProcessor
     * @param EavProcessor $productEavIndexerProcessor
     * @param CategoryRepository $categoryRepository
     * @param CacheFactory $imageCacheFactory
     * @param CollectionProvider $entityCollectionProvider
     * @param LinkTypeProvider $linkTypeProvider
     * @param ProductLinkInterfaceFactory $productLinkFactory
     * @param ProductLinkExtensionFactory $productLinkExtensionFactory
     * @param EntryConverterPool $mediaGalleryEntryConverterPool
     * @param DataObjectHelper $dataObjectHelper
     * @param JoinProcessorInterface $joinProcessor
     * @param Configuration $configuration
     * @param ImageBuilder $imageBuilder
     * @param Emulation $appEmulation
     * @param StockItemRepository $stockItemRepository
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param Configurable $catalogProductTypeConfigurable
     * @param ProductFactory $productFactory
     * @param StockRegistryInterface $stockRegistry
     * @param FeedCategoryFactory $feedCategoryFactory
     * @param Helper $feedHelper
     * @param ResourceModel\CeneoCategory\CollectionFactory $ceneoCollectionFactory
     * @param FeedCategoryMappingCollectionFactory $feedCategoryMappingFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        StoreManagerInterface $storeManager,
        ProductAttributeRepositoryInterface $metadataService,
        Url $url,
        Link $productLink,
        OptionFactory $itemOptionFactory,
        StockItemInterfaceFactory $stockItemFactory,
        ProductOptionFactory $catalogProductOptionFactory,
        Visibility $catalogProductVisibility,
        Status $catalogProductStatus,
        Config $catalogProductMediaConfig,
        Type $catalogProductType,
        Manager $moduleManager,
        Product $catalogProduct,
        ProductResourceModel $resource,
        Collection $resourceCollection,
        CollectionFactory $collectionFactory,
        Filesystem $filesystem,
        IndexerRegistry $indexerRegistry,
        Processor $productFlatIndexerProcessor,
        PriceProcessor $productPriceIndexerProcessor,
        EavProcessor $productEavIndexerProcessor,
        CategoryRepository $categoryRepository,
        CacheFactory $imageCacheFactory,
        CollectionProvider $entityCollectionProvider,
        LinkTypeProvider $linkTypeProvider,
        ProductLinkInterfaceFactory $productLinkFactory,
        ProductLinkExtensionFactory $productLinkExtensionFactory,
        EntryConverterPool $mediaGalleryEntryConverterPool,
        DataObjectHelper $dataObjectHelper,
        JoinProcessorInterface $joinProcessor,
        Configuration $configuration,
        ImageBuilder $imageBuilder,
        Emulation $appEmulation,
        StockItemRepository $stockItemRepository,
        CategoryCollectionFactory $categoryCollectionFactory,
        Configurable $catalogProductTypeConfigurable,
        ProductFactory $productFactory,
        StockRegistryInterface $stockRegistry,
        FeedCategoryFactory $feedCategoryFactory,
        Helper $feedHelper,
        ResourceModel\CeneoCategory\CollectionFactory $ceneoCollectionFactory,
        FeedCategoryMappingCollectionFactory $feedCategoryMappingFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $storeManager,
            $metadataService,
            $url,
            $productLink,
            $itemOptionFactory,
            $stockItemFactory,
            $catalogProductOptionFactory,
            $catalogProductVisibility,
            $catalogProductStatus,
            $catalogProductMediaConfig,
            $catalogProductType,
            $moduleManager,
            $catalogProduct,
            $resource,
            $resourceCollection,
            $collectionFactory,
            $filesystem,
            $indexerRegistry,
            $productFlatIndexerProcessor,
            $productPriceIndexerProcessor,
            $productEavIndexerProcessor,
            $categoryRepository,
            $imageCacheFactory,
            $entityCollectionProvider,
            $linkTypeProvider,
            $productLinkFactory,
            $productLinkExtensionFactory,
            $mediaGalleryEntryConverterPool,
            $dataObjectHelper,
            $joinProcessor,
            $data
        );

        $this->configuration = $configuration;
        $this->imageBuilder = $imageBuilder;
        $this->appEmulation = $appEmulation;
        $this->stockItemRepository = $stockItemRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->productFactory = $productFactory;
        $this->stockRegistry = $stockRegistry;
        $this->ceneoCollectionFactory = $ceneoCollectionFactory;
        $this->feedCategoryMappingFactory = $feedCategoryMappingFactory;
        $this->feedCategoryFactory = $feedCategoryFactory;
    }

    /**
     * Function for initialize model
     */
    protected function _construct()
    {
        $this->_init(ProductResourceModel::class);
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws LocalizedException
     */
    public function __call($method, $args)
    {
        if (in_array(substr($method, 0, 3), self::$prefixes)) {
            return parent::__call($method, $args);
        }

        $result = parent::__call('get' . $method, $args);
        return $result;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        $isInStock = $this->stockRegistry->getStockItem($this->getId())->getIsInStock();

        if (empty($isInStock)) {
            return false;
        }

        $minQty = $this->configuration->getStockMinQty();
        $amount = $this->getQty();

        return (empty($amount) ? false : $amount >= $minQty);
    }

    /**
     * @param string $inStockLabel
     * @param string $outOfStockLabel
     * @return string
     */
    public function getAvailabilityDescription($inStockLabel = 'in stock', $outOfStockLabel = 'out of stock')
    {
        return $this->isAvailable() ? $inStockLabel : $outOfStockLabel;
    }

    /**
     * @return string
     */
    public function getManufacturer()
    {
        return $this->getAttributeText('manufacturer');
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if ($this->getVisibility() != Visibility::VISIBILITY_NOT_VISIBLE) {
            return parent::getUrlInStore();
        }

        $parents = $this->catalogProductTypeConfigurable->getParentIdsByChild($this->getId());
        if (!$parents || count($parents) < 1) {
            return null;
        }

        $parentId = reset($parents);
        if (!isset(self::$parentUrlsCached[$parentId])) {
            $parent = $this->productFactory->create();
            self::$parentUrlsCached[$parentId] = $parent->load($parentId)->getUrlInStore();
        }

        return self::$parentUrlsCached[$parentId];
    }

    /**
     * @return float
     */
    public function getQty()
    {
        try {
            $qty = 0;
            switch ($this->getTypeId()) {
                case Type::TYPE_BUNDLE:
                case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                    throw new \Exception('Bundle and Grouped stock getters are not implemented');
                case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                    /* @var $type \Magento\ConfigurableProduct\Model\Product\Type\Configurable */
                    $type = $this->getTypeInstance();

                    /* @var $associateds \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection */
                    $associateds = $type->getUsedProductCollection($this);
                    $associateds->joinField(
                        'qty',
                        'cataloginventory_stock_item',
                        'qty',
                        'product_id=entity_id',
                        '{{table}}.stock_id=1',
                        'left'
                    );

                    foreach ($associateds as $associated) {
                        /* @var $associated \Magento\Catalog\Model\Product */
                        if ((int)$associated->getQty() > $qty) {
                            $qty = (int)$associated->getQty();
                        }
                    }
                    break;
                case Type::TYPE_SIMPLE:
                case Type::TYPE_VIRTUAL:
                case \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE:
                default:
                    $qty = $this->stockItemRepository->get($this->getId())->getQty();
                    break;
            }
        } catch (NoSuchEntityException $e) {
            $qty = '0';
        } catch (\Exception $e) {
            $qty = '0';
        }

        return $qty;
    }

    /**
     * @param string $pathsSeparator
     * @param string $categoriesSeparator
     * @return string
     * @throws LocalizedException
     */
    public function getCategoriesList(
        $pathsSeparator = self::PATHS_SEPARATOR,
        $categoriesSeparator = self::CATEGORIES_SEPARATOR
    ) {
        $categories = $this->getCategoryCollection();
        $categories->addAttributeToSelect('name');
        $categories->addFieldToFilter('is_active', true);

        $paths = [];
        foreach ($categories as $category) {
            $pathIds = $category->getPathIds();
            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect('name');
            $collection->addFieldToFilter('entity_id', ['in' => $pathIds]);
            $collection->addFieldToFilter('name', ['nin' => $this->configuration->getExcludedCategoriesName()]);

            $path = '';
            foreach ($pathIds as $pathId) {
                if (($item = $collection->getItemById($pathId))) {
                    $path .= $categoriesSeparator . $item->getName();
                }
            }

            $paths[] = trim($path, '/');
        }

        return implode($pathsSeparator, $paths);
    }

    /**
     * Get image of product
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProfileImage()
    {
        if ($this->configuration->useRawImage()) {
            $imageUrl = $this->configuration->getMediaPublicDirectory() . 'catalog/product' . $this->getImage();
        } else {
            $storeId = $this->getStore()->getId();
            $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
            $imageUrl = $this->imageBuilder->setProduct($this)
                ->setImageId('product_base_image')
                ->create()
                ->getImageUrl();
            $this->appEmulation->stopEnvironmentEmulation();
        }

        return $imageUrl;
    }

    /**
     * Generates full Ceneo Category path
     *
     * @param string $fullCategoryPath
     * @param string $ceneoCategoryId
     * @param string $feedData
     * @return string
     */
    public function getFullCategoryPath($fullCategoryPath = '', $ceneoCategoryId = '', $feedData = '')
    {
        if ($ceneoCategoryId === '') {
            $categoryIds = $this->getCategoryIds();
            $ceneoCategoryId = $this->getCeneoCategoryIdByCategoryId($categoryIds, $feedData->getCeneoMappingId());
        }

        if ($ceneoCategoryId) {
            $data = $this->getCeneoCategoryDataByCeneoCategoryId($ceneoCategoryId);
            $fullCategoryPath = $data['name'] . '/' . $fullCategoryPath;
            if (isset($data['parent_id']) && $data['parent_id'] != '') {
                return $this->getFullCategoryPath($fullCategoryPath, $data['parent_id'], $feedData);
            }
        }
        return $fullCategoryPath;
    }

    /**
     * Get Ceneo Category Data by shop Category Id
     *
     * @param $ceneoCategory
     * @param string $dataKey
     * @return mixed
     */
    public function getCeneoCategoryDataByCeneoCategoryId($ceneoCategory, $dataKey = '')
    {
        $ceneoCategoriesCollection = $this->ceneoCollectionFactory->create();
        $ceneoCategoriesCollection->addFieldToFilter('ceneo_id', ['eq' => (int)$ceneoCategory]);
        $ceneoCategoryData = $ceneoCategoriesCollection->getFirstItem()->getData($dataKey);
        return $ceneoCategoryData;
    }

    /**
     * Get Ceneo Category Id by shop Category Id
     *
     * @param $categoryIds
     * @param $feedCategoryId
     * @return bool
     */
    public function getCeneoCategoryIdByCategoryId($categoryIds, $feedCategoryId)
    {
        $ceneoCategoriesMappingCollection = $this->feedCategoryMappingFactory->create();
        $ceneoCategoriesMappingCollection->addFieldToFilter('feed_category_id', ['eq' => (int)$feedCategoryId]);
        $ceneoCategoriesMappingCollection->addFieldToFilter('category_id', ['eq' => reset($categoryIds)]);

        foreach ($ceneoCategoriesMappingCollection as $ceneoMappedCategory) {
            return $ceneoMappedCategory->getData('value');
        }

        return false;
    }

    /**
     * Return category model by id
     *
     * @param $id
     * @return CategoryInterface|mixed
     */
    public function getCategoryById($id)
    {
        try {
            $category = $this->categoryRepository->get($id, $this->getStore()->getId());
            $pathIds = $category->getPathIds();
            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect('name');
            $collection->addFieldToFilter('entity_id', ['in' => $pathIds]);
            $collection->addFieldToFilter('name', ['nin' => $this->configuration->getExcludedCategoriesName()]);

            $path = '';
            foreach ($pathIds as $pathId) {
                if (($item = $collection->getItemById($pathId))) {
                    $path .= self::CATEGORIES_SEPARATOR . $item->getName();
                }
            }

            $paths[] = trim($path, '/');
            return implode(self::PATHS_SEPARATOR, $paths);
        } catch (LocalizedException $e) {
            return null;
        }
    }
}
