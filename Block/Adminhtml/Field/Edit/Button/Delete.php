<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Block\Adminhtml\Field\Edit\Button;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete implements ButtonProviderInterface
{
    /**
     * @var UrlInterface $urlBuilder
     */
    private $urlBuilder;

    /**
     *
     * @var RequestInterface $request
     */
    private $request;

    /**
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     */
    public function __construct(
        UrlInterface $urlBuilder,
        RequestInterface $request
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $id = $this->request->getParam('id');
        if ($id != null) {
            $deleteUrl = $this->urlBuilder->getUrl('*/*/delete', ['id' => $id]);
            return [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to delete?'
                    ) . '\', \'' . $deleteUrl . '\')',
                'sort_order' => 40
            ];
        }

        return [];
    }
}
