<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Helper;

use Ceneo\Feed\Model\Feed;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Configuration extends AbstractHelper
{
    /**
     * Configuration path
     */
    const CONFIG_MODULE_ENABLE = 'feedSection/ceneofeed/enable';

    /**
     * Configuration path
     */
    const CONFIG_FEEDS_DIRECTORY = 'feedSection/ceneofeed/directory';

    /**
     * Configuration path
     */
    const CONFIG_CRON_ENABLE = 'feedSection/ceneofeed/cron_enable';

    /**
     * Configuration path
     */
    const CONFIG_RAW_IMAGES = 'feedSection/ceneofeed/raw_images';

    /**
     * Configuration path
     */
    const CONFIG_URL_API = 'feedSection/ceneofeed/categories_url';

    /**
     * Configuration path
     */
    const CONFIG_EAN_ATTRIBUTE = 'feedSection/ceneofeed/ean';

    /**
     * Configuration path
     */
    const CONFIG_MANUFACTURER_ATTRIBUTE = 'feedSection/ceneofeed/manufacturer';

    /**
     * Configuration path
     */
    const CONFIG_MANUCACTURER_CODE_ATTRIBUTE = 'feedSection/ceneofeed/manufacturer_code';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Configuration constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }

    /**
     * @return boolean
     */
    public function isModuleEnabled()
    {
        return $this->getConfigValue(self::CONFIG_MODULE_ENABLE);
    }

    /**
     * @return boolean
     */
    public function isCronEnabled()
    {
        return $this->getConfigValue(self::CONFIG_CRON_ENABLE);
    }

    /**
     * @return boolean
     */
    public function useRawImage()
    {
        return $this->getConfigValue(self::CONFIG_RAW_IMAGES);
    }

    public function getExcludedCategoriesName()
    {
        return ['Root Catalog', 'Default Category'];
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getMediaPublicDirectory()
    {
        $mediaPublic = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        return $mediaPublic;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getFeedsPublicDirectory()
    {
        $baseUrl = $this->getMediaPublicDirectory();
        $directory = $this->getConfigValue(self::CONFIG_FEEDS_DIRECTORY);

        $dir = rtrim($baseUrl, '/') . '/' . trim($directory, '/') . '/';
        return $dir;
    }

    /**
     * @return string
     */
    public function getEanAttribute()
    {
        return $this->getConfigValue(self::CONFIG_EAN_ATTRIBUTE);
    }

    /**
     * @return string
     */
    public function getManufacturerAttribute()
    {
        return $this->getConfigValue(self::CONFIG_MANUFACTURER_ATTRIBUTE);
    }

    /**
     * @return string
     */
    public function getManufacturerCodeAttribute()
    {
        return $this->getConfigValue(self::CONFIG_MANUCACTURER_CODE_ATTRIBUTE);
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    public function getFeedsDirectory()
    {
        $directory = $this->getConfigValue(self::CONFIG_FEEDS_DIRECTORY);
        $public = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath();
        $directory = '/' . trim($public, '/') . '/' . trim($directory, '/') . '/';
        return $directory;
    }

    /**
     * @param Feed $feed
     * @return string
     * @throws FileSystemException
     */
    public function getFeedPath(Feed $feed)
    {
        $filename = $feed->getFilename();
        $directory = $this->createFeedPath($filename);
        return $directory;
    }

    public function getCeneoCategoriesApiUrl()
    {
        return $this->getConfigValue(self::CONFIG_URL_API);
    }

    /**
     * @param $filename
     * @return string
     * @throws FileSystemException
     */
    public function createFeedPath($filename)
    {
        $directory = $this->getFeedsDirectory();
        $directory = '/' . trim($directory, '/') . '/' . ltrim($filename, '/');
        return $directory;
    }

    /**
     * @return string
     */
    public function getStockMinQty()
    {
        $value = $this->getConfigValue('cataloginventory_item/options/min_qty');
        return $value;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseUrl()
    {
        $baseUrl = rtrim($this->storeManager->getStore()->getBaseUrl(), '/');
        return $baseUrl;
    }

    /**
     * @param $name
     * @return mixed
     */
    private function getConfigValue($name)
    {
        return $this->scopeConfig->getValue($name, ScopeInterface::SCOPE_STORE);
    }
}
