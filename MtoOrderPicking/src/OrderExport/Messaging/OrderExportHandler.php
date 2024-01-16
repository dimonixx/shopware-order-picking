<?php

namespace MtoOrderPicking\OrderExport\Messaging;

use MtoOrderPicking\OrderExport\OrderExporter;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: OrderExportMessage::class)]
class OrderExportHandler
{
    public function __construct(protected OrderExporter $orderExporter)
    {
    }

    public function __invoke(OrderExportMessage $orderExportMessage): void
    {
        $exportResult = $this->orderExporter->export(
            $orderExportMessage->getOrderId(),
            $orderExportMessage->getContext()
        );

        if ($exportResult !== OrderExporter::EXPORT_SUCCESSFUL) {

        }
    }
}
