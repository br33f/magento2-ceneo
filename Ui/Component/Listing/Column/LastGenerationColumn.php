<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Ui\Component\Listing\Column;

use Ceneo\Feed\Helper\Configuration;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class LastGenerationColumn extends Column
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * UrlColumn constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Configuration $configuration
     * @param DateTime $date
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Configuration $configuration,
        DateTime $date,
        array $components = [],
        array $data = []
    ) {
        $this->configuration = $configuration;
        $this->date = $date;
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
        foreach ($dataSource['data']['items'] as &$item) {
            $path = $this->configuration->createFeedPath($item['filename']);
            if (!file_exists($path)) {
                $mtime = __('File not exists');
            } elseif (!is_readable($path)) {
                $mtime = __('Unable to read');
            } else {
                $mtime = $this->date->date('Y-m-d H:i:s', filemtime($path));
            }

            $item[$name] = $mtime;
        }

        return $dataSource;
    }
}
