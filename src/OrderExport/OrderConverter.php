<?php

namespace MtoOrderPicking\OrderExport;

use Shopware\Core\Checkout\Order\OrderEntity;
use MtoOrderPicking\Api\Model\Order;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class OrderConverter
{
    public function __construct(protected SerializerInterface $serializer)
    {
    }

    public function convert(OrderEntity $orderEntity): string
    {
        $order = new Order($orderEntity);

        return $this->serializer->serialize(
            $order,
            'json',
            [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]
        );
    }
}