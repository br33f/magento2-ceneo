<?php
/**
 * @copyright Copyright (c) 2019 Aurora Creation Sp. z o.o. (https://auroracreation.com)
 */

namespace Ceneo\Feed\Model\MessageBroker;

interface MessageBrokerInterface
{
    public function publishMessage($message, $success = true);
}
