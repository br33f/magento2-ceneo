<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Controller\Adminhtml\Feeds;

use Ceneo\Feed\Model\Definitions;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Zend\Http\Request;

class Edit extends RuleBase
{
    /**
     * @return $this|ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = __('Add feed');

        $this->_initRule();

        $model = $this->coreRegistry->registry(Definitions::RULE_REGISTRY_KEY);
        if ($this->getRequest()->getMethod() == Request::METHOD_POST) {
            $title = __('Edit feed');
            $data = $this->getRequest()->getPostValue();
            $data['conditions'] = $data['rule']['conditions'];
            $data['additional_attributes'] = $this->formatAdditionalAttributes();
            unset($data['rule']);
            $model->loadPost($data);
            $model->save();
            return $this->resultRedirectFactory->create()->setPath('ceneofeed/feeds/index');
        }

        $resultPage->getConfig()
            ->getTitle()
            ->prepend($title);

        return $resultPage;
    }

    /**
     * @return string
     */
    private function formatAdditionalAttributes()
    {
        $additionalAttributes = $this->getRequest()->getParam('additional_attributes');

        return (is_array($additionalAttributes) && !empty($additionalAttributes) ? implode(',', $additionalAttributes) : '');
    }

}
