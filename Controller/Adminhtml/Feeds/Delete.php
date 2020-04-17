<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Controller\Adminhtml\Feeds;

use Ceneo\Feed\Model\FeedFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;

class Delete extends Action
{
    /**
     * @var FeedFactory
     */
    protected $factory;

    /**
     * @param Context $context
     * @param FeedFactory $factory
     */
    public function __construct(
        Context $context,
        FeedFactory $factory
    ) {
        $this->factory = $factory;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $modelId = $this->getRequest()->getParam('id');

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($modelId) {
            try {
                $model = $this->factory->create();
                $resource = $model->getResource();
                $resource->load($model, $modelId);
                $resource->delete($model);

                $this->messageManager->addSuccess(__('The feed has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Unexpected error occured.'));
            }
        } else {
            $this->messageManager->addError(__("We can't find a feed to delete."));
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ceneo_Feed::module');
    }
}
