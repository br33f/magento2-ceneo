<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Block\Adminhtml\Field\Edit\Button;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ResetCeneoData implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $resetUrl = $this->urlBuilder->getUrl('ceneofeed/mapping/reset');
        return [
            'label' => __('Reset Ceneo Data'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to reset Ceneo Data?'
                ) . '\', \'' . $resetUrl . '\')',
            'sort_order' => 40
        ];
    }
}
