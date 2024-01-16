<?php

namespace MtoOrderPicking\OrderExport;

use MtoOrderPicking\Api\ClientFactoryInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Util\Json;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;

class OrderExporter
{
    public const CONFIG_PATH_API_ORDER_EXPORT = 'MtoOrderPicking.config.apiEndpointOrderExport';

    public const EXPORT_SUCCESSFUL = 1;

    public const EXPORT_FAILED = 0;

    public function __construct(
        protected ClientFactoryInterface $clientFactory,
        protected EntityRepository $entityRepository,
        protected OrderConverter $orderConverter,
        protected SystemConfigService $configService
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

                $uri = $this->configService->get(self::CONFIG_PATH_API_ORDER_EXPORT);

                $client = $this->clientFactory->create();
                $response = $client->request(
                    $uri,
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
