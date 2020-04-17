<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Ui\Component\Feed\Form\Categories;

use Ceneo\Feed\Helper\CeneoData;
use Ceneo\Feed\Helper\Configuration;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Options tree for "Categories" field
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    public $categoriesTree;
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var CeneoData
     */
    private $ceneoData;

    public function __construct(
        CeneoData $ceneoData,
        Configuration $configuration
    ) {
        $this->configuration = $configuration;
        $this->ceneoData = $ceneoData;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getCategoriesTree();
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     */
    protected function getCategoriesTree()
    {
        $categoriesTree = $this->processCategories($this->getCeneoCategories());
        $this->categoriesTree = [
            [
                'is_active' => '0',
                'label' => __('Categories'),
                'value' => '1',
                'optgroup' => reset($categoriesTree)
            ]
        ];

        return $this->categoriesTree;
    }

    /**
     * @param $categories
     * @return array
     */
    private function processCategories($categories)
    {
        $categoryNode = [];

        foreach ($categories as $key => $category) {
            if (isset($category['Id']) && is_array($category['Subcategories'])) {
                $data = [
                    'value' => $category['Id'],
                    'is_active' => '1',
                    'label' => $category['Name'],
                ];

                if (isset($category['Subcategories']) && is_array($category['Subcategories'])) {
                    $data['optgroup'] = $this->processCategories($category['Subcategories']);

                    if ((isset($data['optgroup'][0])) && (!isset($data['optgroup'][0]['label']))) {
                        $data['optgroup'] = reset($data['optgroup']);
                    }
                }
            } else {
                $data = $this->processCategories($category);
            }

            $categoryNode[] = $data;
        }

        return $categoryNode;
    }

    /**
     * @return mixed
     */
    private function getCeneoCategories()
    {
        $ceneoCategoriesXML = file_get_contents($this->configuration->getCeneoCategoriesApiUrl());
        $xml = simplexml_load_string($ceneoCategoriesXML, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        return json_decode($json, true);
    }
}
