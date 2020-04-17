<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Controller\Adminhtml\Feeds;

use Ceneo\Feed\Helper\Configuration;
use Ceneo\Feed\Model\Definitions;
use Ceneo\Feed\Model\FeedFactory;
use Ceneo\Feed\Model\Generator;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;

class Generate extends RuleBase
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FeedFactory $feedFactory
     * @param Generator $generator
     * @param Configuration $configuration
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FeedFactory $feedFactory,
        Generator $generator,
        Configuration $configuration
    ) {
        parent::__construct($context, $coreRegistry, $feedFactory);
        $this->generator = $generator;
        $this->configuration = $configuration;
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        try {
            if (!$this->configuration->isModuleEnabled()) {
                throw new \Exception(__("Generation is disabled by configuration"));
            }

            $this->_initRule();

            $model = $this->coreRegistry->registry(Definitions::RULE_REGISTRY_KEY);
            if (!$model->getId()) {
                $this->messageManager->addError(__("We can't find a feed to generate."));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }

            $this->generator->setFeedId($model->getId());
            $this->generator->setForce(true);
            $this->generator->setGenerationDelay(0);
            $this->generator->run();

            $this->messageManager->addSuccess(__('The feed has been generated.'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create()->setPath('*/*/');
        return $resultRedirect;
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
