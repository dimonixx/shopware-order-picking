<?php

namespace MtoOrderPicking\OrderExport;

use MtoOrderPicking\Api\ClientFactoryInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Util\Json;
use Symfony\Component\HttpFoundation\Request;

class OrderExporter
{
    public const EXPORT_SUCCESSFUL = 1;

    public const EXPORT_FAILED = 0;

    public function __construct(
        protected ClientFactoryInterface $clientFactory,
        protected EntityRepository $entityRepository,
        protected OrderConverter $orderConverter
    ) {
    }

    public function export(string $orderId, Context $context): int
    {
        $criteria = new Criteria([$orderId]);
        $criteria->addAssociations(
            ['orderCustomer', 'billingAddress', 'billingAddress.country']
        );
        $criteria->addAssociations([
            'deliveries', 'deliveries.shippingOrderAddress', 'deliveries.shippingOrderAddress.country'
        ]);
        $criteria->addAssociations(['lineItems', 'lineItems.product']);

        $order = $this->entityRepository->search($criteria, $context)->first();

        if ($order instanceof OrderEntity) {
            try {
                $orderConvertedData = $this->orderConverter->convert($order);

                $client = $this->clientFactory->create();
                $response = $client->request(
                    '',
                    Request::METHOD_POST,
                    [
                        'headers' => [
                            'Context-Type' => 'application/json'
                        ],
                        'body' => $orderConvertedData
                    ]
                );

                $responseData = json_decode($response, true);

                return self::EXPORT_SUCCESSFUL;
            } catch (\Exception $exception) {


                return self::EXPORT_FAILED;
            }
        }
    }
}
