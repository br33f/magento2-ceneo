<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Ui\Component\Listing\Column;

use Ceneo\Feed\Helper\Configuration;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class UrlColumn extends Column
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * UrlColumn constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Configuration $configuration
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Configuration $configuration,
        array $components = [],
        array $data = []
    ) {
        $this->configuration = $configuration;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $name = $this->getData('name');
        $directory = $this->configuration->getFeedsPublicDirectory();
        foreach ($dataSource['data']['items'] as &$item) {
            $url = $directory . $item['filename'];
            $item[$name] = "<a href=\"{$url}\" target=\"_blank\">{$url}</a>";
        }

        return $dataSource;
    }
}
