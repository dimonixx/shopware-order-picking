<?php

namespace MtoOrderPicking\OrderExport;

use MtoOrderPicking\Api\ClientFactoryInterface;
use Psr\Log\LoggerInterface;
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
        protected EntityRepository $orderRepository,
        protected OrderConverter $orderConverter,
        protected SystemConfigService $configService,
        protected LoggerInterface $logger,
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

        $order = $this->orderRepository->search($criteria, $context)->first();

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

                if (array_key_exists('status', $responseData) && (int) $responseData['status']['code'] === 200) {
                    $customFields = $order->getCustomFields();

                    $customFields['order_number'] = $responseData['orderNumber'];
                    $customFields['exported'] = 1;

                    $order->setCustomFields($customFields);

                    $this->orderRepository->update(
                        [
                            [
                                'id' => $order->getId(),
                                'customFields' => $customFields
                            ]
                        ],
                        $context
                    );
                }

                return self::EXPORT_SUCCESSFUL;
            } catch (\Exception $exception) {
                $this->logger->error(
                    sprintf('Order #%s could not be exported : %s', $order->getOrderNumber(), $exception->getMessage()),
                    []
                );

                return self::EXPORT_FAILED;
            }
        }
    }
}
