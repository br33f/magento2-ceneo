<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Ui\Component\Listing\Column;

use Ceneo\Feed\Model\ResourceModel\Feed;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Actions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
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
            $item[$name]['edit'] = [
                'label' => __('Edit'),
                'href' => $this->urlBuilder->getUrl('ceneofeed/feeds/edit', [
                    'id' => $item[Feed::PRIMARY_KEY_NAME]
                ]),
            ];
            $item[$name]['delete'] = [
                'label' => __('Delete'),
                'href' => $this->urlBuilder->getUrl('ceneofeed/feeds/delete', [
                    'id' => $item[Feed::PRIMARY_KEY_NAME]
                ]),
                'confirm' => [
                    'title' => __('Delete ${ $.$data.title }'),
                    'message' => __('Are you sure you wan\'t to do that action on a ${ $.$data.title } record?')
                ],
            ];
            $item[$name]['generate'] = [
                'label' => __('Generate'),
                'href' => $this->urlBuilder->getUrl('ceneofeed/feeds/generate', [
                    'id' => $item[Feed::PRIMARY_KEY_NAME]
                ]),
                'confirm' => [
                    'title' => __('Generate ${ $.$data.title }'),
                    'message' => __('Are you sure you wan\'t to do that action on a ${ $.$data.title } record?')
                ],
            ];
        }

        return $dataSource;
    }
}
