<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Controller\Adminhtml\Feeds;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 *
 * @package Ceneo\Feed\Controller\Adminhtml\Feeds
 * @author L.Paliwoda <l.paliwoda@auroracreation.com>
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('Ceneo_Feed::feeds');
        $resultPage->addBreadcrumb(__('Product tabs'), __('Product feeds'));
        $resultPage->getConfig()->getTitle()->prepend(__('Product feeds'));

        return $resultPage;
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
