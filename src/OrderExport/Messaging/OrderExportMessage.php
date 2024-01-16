<?php

namespace MtoOrderPicking\OrderExport\Messaging;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\MessageQueue\AsyncMessageInterface;

class OrderExportMessage implements AsyncMessageInterface
{
    public function __construct(
        protected Context $context,
        protected string $orderId
    ) {
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }
}
