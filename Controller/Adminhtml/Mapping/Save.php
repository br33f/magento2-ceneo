<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Controller\Adminhtml\Mapping;

use Ceneo\Feed\Helper\Data as Helper;
use Ceneo\Feed\Model\FeedCategoryFactory;
use Ceneo\Feed\Model\FeedCategoryMappingFactory;
use Ceneo\Feed\Model\ResourceModel\FeedCategory;
use Ceneo\Feed\Model\ResourceModel\FeedCategoryMappingFactory as FeedCategoryMappingResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Save extends Action
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var FeedCategoryFactory
     */
    protected $feedCategoryMapping;

    /**
     * @var FeedCategoryResourceModel
     */
    protected $feedCategoryMappingResource;

    /**
     * @var FeedCategoryFactory
     */
    protected $feedCategoryFactory;

    public function __construct(
        Context $context,
        FeedCategoryMappingFactory $feedCategoryMapping,
        FeedCategoryMappingResourceModel $feedCategoryMappingResource,
        FeedCategoryFactory $feedCategoryFactory,
        Helper $helper
    ) {
        parent::__construct($context);

        $this->helper = $helper;
        $this->feedCategoryMapping = $feedCategoryMapping;
        $this->feedCategoryMappingResource = $feedCategoryMappingResource;
        $this->feedCategoryFactory = $feedCategoryFactory;
    }

    /**
     * Save mapping action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam(FeedCategory::PRIMARY_KEY_NAME);
            $code = $this->getRequest()->getParam('code');
            $name = $this->getRequest()->getParam('name');

            if ($id) {
                $feedCategory = $this->feedCategoryFactory->create();
                $feedCategory->setData([
                    'feed_category_id' => $id,
                    'code' => $code,
                    'exclude_categories' => $this->getExcludeCategories(),
                    'name' => $name
                ]);
                $resource = $feedCategory->getResource();
                $resource->save($feedCategory);

                $mappedCategoriesData = $this->getRequest()->getParam('mapped_categories_container');
                if (is_array($mappedCategoriesData) && !empty($mappedCategoriesData)) {
                    if ($id) {
                        $feedCategoryMapping = $this->feedCategoryMappingResource->create();
                        $feedCategoryMapping->deleteCategoryMapping($id);
                    }
                    $formattedArray = $this->helper->formatInsertArray($mappedCategoriesData);

                    if (empty($formattedArray)) {
                        $this->messageManager->addErrorMessage(__('Given data is empty'));
                        $this->_redirect('*/*/index');
                        return;
                    }

                    $feedCategoryMapping->insertMultiple($formattedArray);
                }
            } else {
                $feedCategory = $this->feedCategoryFactory->create();
                $feedCategory->setData([
                    'code' => $code,
                    'exclude_categories' => $this->getExcludeCategories(),
                    'name' => $name
                ]);
                $resource = $feedCategory->getResource();
                $resource->save($feedCategory);
            }

            $this->messageManager->addSuccessMessage(__('Rows have been saved successfully'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $this->_redirect('*/*/index');
    }

    private function getExcludeCategories()
    {
        $excludeCategories = $this->getRequest()->getParam('exclude_categories');
        if (is_array($excludeCategories)) {
            $excludeCategories = implode(',', $excludeCategories);
        }

        return $excludeCategories;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ceneo_Feeds::module');
    }
}
