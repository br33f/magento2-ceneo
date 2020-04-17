<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Controller\Adminhtml\Feeds;

use Ceneo\Feed\Model\Definitions;
use Ceneo\Feed\Model\FeedFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

abstract class RuleBase extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FeedFactory $feedFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FeedFactory $feedFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->feedFactory = $feedFactory;
    }

    /**
     * Initiate rule
     *
     * @return void
     */
    protected function _initRule()
    {
        $model = $this->feedFactory->create();

        if (($id = $this->getRequest()->getParam('id', null)) !== null) {
            $model->load((int)$id);
            $model->getConditions()->setJsFormObject(
                $model->getConditionsFieldSetId($model->getConditions()->getFormName())
            );
        }

        $this->coreRegistry->register(Definitions::RULE_REGISTRY_KEY, $model);
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ceneo_Feed::module');
    }
}
