<?php

namespace MtoOrderPicking\Api\Provider;

use MtoOrderPicking\Api\Model\CustomerOrderPicking;
use MtoOrderPicking\Api\Model\PickingListProduct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

interface DataProviderInterface
{
    public function getCustomerOrderPicking(SalesChannelContext $context): ?CustomerOrderPicking;

    public function getProduct(string $productSku, SalesChannelContext $context): ?PickingListProduct;
}