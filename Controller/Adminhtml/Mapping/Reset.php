<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Controller\Adminhtml\Mapping;

use Ceneo\Feed\Helper\CeneoData;
use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class Reset extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CeneoData
     */
    private $ceneoData;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CeneoData $ceneoData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CeneoData $ceneoData
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->ceneoData = $ceneoData;
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->ceneoData->saveCeneoCategoriesData();
            $this->messageManager->addSuccessMessage('Ceneo Data has been reset');
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage("Ceneo Data hasn't been reset");
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
        return true;
    }
}
