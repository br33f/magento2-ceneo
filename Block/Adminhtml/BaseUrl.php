<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;

class BaseUrl extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Ceneo_Feed::config.phtml';

    /**
     * @var Reader
     */
    protected $_configReader;

    /**
     * BaseUrl constructor.
     *
     * @param Template\Context $context
     * @param Reader $reader
     * @param array $data
     */
    public function __construct(Template\Context $context, Reader $reader, array $data = [])
    {
        parent::__construct($context, $data);
        $this->_configReader = $reader;
    }

    /**
     * @return string
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function getAdminBaseUrl()
    {
        $config = $this->_configReader->load();
        $adminSuffix = $config['backend']['frontName'];
        return $this->getBaseUrl() . $adminSuffix . '/';
    }
}
