<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Controller\Adminhtml\Mapping;

use Ceneo\Feed\Model\FeedCategoryFactory;
use Ceneo\Feed\Model\ResourceModel\FeedCategory;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class Delete extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var FeedCategory
     */
    private $feedCategoryResource;

    /**
     * @var FeedCategoryFactory
     */
    private $feedCategoryFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param FeedCategory $feedCategoryResource
     * @param FeedCategoryFactory $feedCategoryFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        FeedCategory $feedCategoryResource,
        FeedCategoryFactory $feedCategoryFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;

        $this->feedCategoryResource = $feedCategoryResource;
        $this->feedCategoryFactory = $feedCategoryFactory;
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $feedId = $this->getRequest()->getParam('feed_category_id');

        if ($feedId) {
            /** @var \Ceneo\Feed\Model\FeedCategory $model */
            $model = $this->feedCategoryFactory->create();
            $this->feedCategoryResource->load($model, $feedId);
            try {
                $this->feedCategoryResource->delete($model);
                $this->messageManager->addSuccessMessage('The feed mapping has been deleted');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage("We can't find this mapping to delete.");
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check admin permissions for this controller
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ceneo_Feed::module');
    }
}
