<?php

/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Test\Unit\Model;

/**
 * Class FeedTest
 *
 * @package Ceneo\Feed\Test\Unit\Model
 * @author J.Szczubełek <j.szczubelek@auroracreation.com>
 */
class ProductAdapterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Ceneo\Feed\Model\ProductAdapter
     */
    protected $testClass;

    /**
     * Set up test data
     */
    public function setUp()
    {
        $objectManagerMock = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        \Magento\Framework\App\ObjectManager::setInstance($objectManagerMock);
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $contextMock = $this->createMock(\Magento\Framework\Model\Context::class);
        $registryMock = $this->createMock(\Magento\Framework\Registry::class);
        $extensionFactoryMock = $this->createMock(\Magento\Framework\Api\ExtensionAttributesFactory::class);
        $customAttributeFactoryMock = $this->createMock(\Magento\Framework\Api\AttributeValueFactory::class);
        $storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $storeManagerMock
            ->method('getStore')
            ->willReturn($this->createMock(\Magento\Store\Model\Store::class));
        $metadataServiceMock = $this->createMock(\Magento\Catalog\Api\ProductAttributeRepositoryInterface::class);
        $urlMock = $this->createMock(\Magento\Catalog\Model\Product\Url::class);
        $productLinkMock = $this->createMock(\Magento\Catalog\Model\Product\Link::class);
        $itemOptionFactoryMock = $this->getMockBuilder(\Magento\Catalog\Model\Product\Configuration\Item\OptionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $stockItemFactoryMock = $this->getMockBuilder(\Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory::class)
            ->disableOriginalConstructor()->getMock();
        $catalogProductOptionFactoryMock = $this->getMockBuilder(\Magento\Catalog\Model\Product\OptionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $catalogProductVisibilityMock = $this->createMock(\Magento\Catalog\Model\Product\Visibility::class);
        $catalogProductStatusMock = $this->createMock(\Magento\Catalog\Model\Product\Attribute\Source\Status::class);
        $catalogProductMediaConfigMock = $this->createMock(\Magento\Catalog\Model\Product\Media\Config::class);
        $catalogProductTypeMock = $this->createMock(\Magento\Catalog\Model\Product\Type::class);
        $moduleManagerMock = $this->createMock(\Magento\Framework\Module\Manager::class);
        $catalogProductMock = $this->createMock(\Magento\Catalog\Helper\Product::class);
        $resourceMock = $this->getMockForAbstractClass(
            \Magento\Catalog\Model\ResourceModel\Product::class,
            [],
            '',
            false,
            true,
            true,
            ['getQty', 'getEntityType', 'getCategoryCollection']
        );
        $resourceCollectionMock = $this->createMock(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        $collectionFactoryMock = $this->getMockBuilder(\Magento\Framework\Data\CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $filesystemMock = $this->createMock(\Magento\Framework\Filesystem::class);
        $indexerRegistryMock = $this->createMock(\Magento\Framework\Indexer\IndexerRegistry::class);
        $productFlatIndexerProcessorMock = $this->createMock(\Magento\Catalog\Model\Indexer\Product\Flat\Processor::class);
        $productPriceIndexerProcessorMock = $this->createMock(\Magento\Catalog\Model\Indexer\Product\Price\Processor::class);
        $productEavIndexerProcessorMock = $this->createMock(\Magento\Catalog\Model\Indexer\Product\Eav\Processor::class);
        $categoryRepositoryMock = $this->createMock(\Magento\Catalog\Model\CategoryRepository::class);
        $imageCacheFactoryMock = $this->getMockBuilder(\Magento\Catalog\Model\Product\Image\CacheFactory::class)
            ->disableOriginalConstructor()->getMock();
        $entityCollectionProviderMock = $this->createMock(\Magento\Catalog\Model\ProductLink\CollectionProvider::class);
        $linkTypeProviderMock = $this->createMock(\Magento\Catalog\Model\Product\LinkTypeProvider::class);
        $productLinkFactoryMock = $this->getMockBuilder(\Magento\Catalog\Api\Data\ProductLinkInterfaceFactory::class)
            ->disableOriginalConstructor()->getMock();
        $productLinkExtensionFactoryMock = $this->getMockBuilder(\Magento\Catalog\Api\Data\ProductLinkExtensionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $mediaGalleryEntryConverterPoolMock = $this->createMock(\Magento\Catalog\Model\Product\Attribute\Backend\Media\EntryConverterPool::class);
        $dataObjectHelperMock = $this->createMock(\Magento\Framework\Api\DataObjectHelper::class);
        $joinProcessorMock = $this->createMock(\Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface::class);
        $configurationMock = $this->createMock(\Ceneo\Feed\Helper\Configuration::class);
        $imageBuilderMock = $this->createMock(\Magento\Catalog\Block\Product\ImageBuilder::class);
        $imageBuilderMock
            ->method('setProduct')
            ->willReturn($imageBuilderMock);
        $imageBuilderMock
            ->method('setImageId')
            ->willReturn($imageBuilderMock);
        $imageBuilderMock
            ->method('create')
            ->willReturn($this->createMock(\Magento\Catalog\Block\Product\Image::class));
        $appEmulationMock = $this->createMock(\Magento\Store\Model\App\Emulation::class);
        $stockItemRepositoryMock = $this->createMock(\Magento\CatalogInventory\Model\Stock\StockItemRepository::class);
        $resourceMock
            ->method('getEntityType')
            ->willReturn($this->createMock(\Magento\Eav\Model\Entity\Type::class));
        $dataCollectionMock = $this->getMockForAbstractClass(
            \Magento\Framework\Data\Collection::class,
            [],
            '',
            false,
            true,
            true,
            ['addAttributeToSelect', 'addFieldToFilter']
        );
        $dataCollectionMock
            ->method('addFieldToFilter')
            ->willReturn(null);
        $resourceMock
            ->method('getCategoryCollection')
            ->willReturn($dataCollectionMock);
        $resourceMock
            ->method('getQty')
            ->willReturn(1);
        $stockItemRepositoryMock
            ->method('get')
            ->willReturn($resourceMock);
        $categoryCollectionFactoryMock = $this->getMockBuilder(\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $catalogProductTypeConfigurableMock = $this->createMock(\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable::class);
        $productFactoryMock = $this->getMockBuilder(\Magento\Catalog\Model\ProductFactory::class)
            ->disableOriginalConstructor()->getMock();
        $feedCategoryFactoryMock = $this->getMockBuilder(\Ceneo\Feed\Model\FeedCategoryFactory::class)
            ->disableOriginalConstructor()->getMock();
        $feedHelperMock = $this->createMock(\Ceneo\Feed\Helper\Data::class);
        $this->testClass = $this->objectManager->getObject(
            \Ceneo\Feed\Model\ProductAdapter::class,
            [
                'context' => $contextMock,
                'registry' => $registryMock,
                'extensionFactory' => $extensionFactoryMock,
                'customAttributeFactory' => $customAttributeFactoryMock,
                'storeManager' => $storeManagerMock,
                'metadataService' => $metadataServiceMock,
                'url' => $urlMock,
                'productLink' => $productLinkMock,
                'itemOptionFactory' => $itemOptionFactoryMock,
                'stockItemFactory' => $stockItemFactoryMock,
                'catalogProductOptionFactory' => $catalogProductOptionFactoryMock,
                'catalogProductVisibility' => $catalogProductVisibilityMock,
                'catalogProductStatus' => $catalogProductStatusMock,
                'catalogProductMediaConfig' => $catalogProductMediaConfigMock,
                'catalogProductType' => $catalogProductTypeMock,
                'moduleManager' => $moduleManagerMock,
                'catalogProduct' => $catalogProductMock,
                'resource' => $resourceMock,
                'resourceCollection' => $resourceCollectionMock,
                'collectionFactory' => $collectionFactoryMock,
                'filesystem' => $filesystemMock,
                'indexerRegistry' => $indexerRegistryMock,
                'productFlatIndexerProcessor' => $productFlatIndexerProcessorMock,
                'productPriceIndexerProcessor' => $productPriceIndexerProcessorMock,
                'productEavIndexerProcessor' => $productEavIndexerProcessorMock,
                'categoryRepository' => $categoryRepositoryMock,
                'imageCacheFactory' => $imageCacheFactoryMock,
                'entityCollectionProvider' => $entityCollectionProviderMock,
                'linkTypeProvider' => $linkTypeProviderMock,
                'productLinkFactory' => $productLinkFactoryMock,
                'productLinkExtensionFactory' => $productLinkExtensionFactoryMock,
                'mediaGalleryEntryConverterPool' => $mediaGalleryEntryConverterPoolMock,
                'dataObjectHelper' => $dataObjectHelperMock,
                'joinProcessor' => $joinProcessorMock,
                'configuration' => $configurationMock,
                'imageBuilder' => $imageBuilderMock,
                'appEmulation' => $appEmulationMock,
                'stockItemRepository' => $stockItemRepositoryMock,
                'categoryCollectionFactory' => $categoryCollectionFactoryMock,
                'catalogProductTypeConfigurable' => $catalogProductTypeConfigurableMock,
                'productFactory' => $productFactoryMock,
                'feedCategoryFactory' => $feedCategoryFactoryMock,
                'feedHelper' => $feedHelperMock
            ]
        );
    }

    /**
     * Test function
     */
    public function testGetUrl()
    {
        $this->assertEquals(null, $this->testClass->getUrl());
    }

    /**
     * Test function
     */
    public function testGetQty()
    {
        $this->assertEquals(1, $this->testClass->getQty());
    }

    /**
     * Test function
     */
    public function testGetCategoreisList()
    {
        $this->assertEquals('', $this->testClass->getCategoreisList());
    }

    /**
     * Test function
     */
    public function testGetProfileImage()
    {
        $this->assertEquals(null, $this->testClass->getProfileImage());
    }
}
