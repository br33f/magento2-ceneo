<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Helper;

use Ceneo\Feed\Model\CeneoCategoryFactory;
use Ceneo\Feed\Model\ResourceModel\CeneoCategory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;

class CeneoData extends AbstractHelper
{
    const DEFAULT_CENEO_URL = 'https://developers.ceneo.pl/api/v3/kategorie';
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var CeneoCategory
     */
    private $ceneoCategoryFactory;

    /**
     * @var CeneoCategory
     */
    private $ceneoCategoryResource;

    /**
     * @var DataObjectFactory
     */
    private $objectFactory;

    /**
     * CeneoData constructor.
     *
     * @param Context $context
     * @param Configuration $configuration
     * @param DataObjectFactory $objectFactory
     * @param CeneoCategoryFactory $ceneoCategoryFactory
     * @param CeneoCategory $ceneoCategoryResource
     */
    public function __construct(
        Context $context,
        Configuration $configuration,
        DataObjectFactory $objectFactory,
        CeneoCategoryFactory $ceneoCategoryFactory,
        CeneoCategory $ceneoCategoryResource
    ) {
        parent::__construct($context);
        $this->configuration = $configuration;
        $this->ceneoCategoryFactory = $ceneoCategoryFactory;
        $this->ceneoCategoryResource = $ceneoCategoryResource;
        $this->objectFactory = $objectFactory;
    }

    /**
     * Truncates and populates Ceneo Categories Data in db
     *
     * @return array
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    public function saveCeneoCategoriesData()
    {
        $connection = $this->ceneoCategoryResource->getConnection();
        $table = $this->ceneoCategoryResource->getMainTable();
        $connection->truncateTable($table);
        return $this->processCategories($this->getCeneoCategories());
    }

    /**
     * @param $categories
     * @param string $parent
     * @return array
     * @throws AlreadyExistsException
     */
    public function processCategories($categories, $parent = '')
    {
        $categoryNode = [];

        foreach ($categories as $key => $category) {
            $categoryData = $this->getCategoryDataObject($category);
            
            if (is_array($categoryData->getData('Subcategories'))) {
                $ceneoCategoryModel = $this->ceneoCategoryFactory->create();

                $ceneoCategoryModel->setName($category['Name']);
                $ceneoCategoryModel->setCeneoId($category['Id']);
                if (isset($category['main']) && $category['main']) {
                    $parent = '';
                }
                $ceneoCategoryModel->setParentId($parent);

                $this->ceneoCategoryResource->save($ceneoCategoryModel);

                $data = [
                    'value' => $category['Id'],
                    'label' => $category['Name'],
                ];

                if ($category['Subcategories'] && is_array($category['Subcategories'])) {
                    $data['optgroup'] = $this->processCategories($category['Subcategories'], $category['Id']);

                    if ((isset($data['optgroup'][0])) && (!isset($data['optgroup'][0]['label']))) {
                        $data['optgroup'] = reset($data['optgroup']);
                    }
                }
            } else {
                $data = $this->processCategories($category, $parent);
            }

            $categoryNode[] = $data;
        }

        return $categoryNode;
    }

    /**
     * Converts category data to DataObject
     *
     * @param $category
     * @return DataObject
     */
    private function getCategoryDataObject($category)
    {
        $obj = $this->objectFactory->create();
        if (isset($category['Id'])) {
            $obj->setData($category);
        }
        return $obj;
    }

    /**
     * Get array of Ceneo categories from remote xml
     *
     * @return mixed
     */
    public function getCeneoCategories()
    {
        $configUrl = $this->configuration->getCeneoCategoriesApiUrl();
        $url = ($configUrl ? $configUrl : self::DEFAULT_CENEO_URL);
        $ceneoCategoriesXML = file_get_contents($url);
        $xml = simplexml_load_string($ceneoCategoriesXML, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        foreach ($array['Category'] as $key => $value) {
            $array['Category'][$key]['main'] = true;
        }

        return $array;
    }
}
