<?php

namespace MtoOrderPicking\OrderPicking\Cart;

use MtoOrderPicking\Api\Provider\DataProviderInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class OrderPickingCollector implements CartDataCollectorInterface
{


    public function __construct(
        protected EntityRepository $productRepository,
        protected DataProviderInterface $dataProvider
    ) {
    }

    public function collect(
        CartDataCollection $data,
        Cart $original,
        SalesChannelContext $context,
        CartBehavior $behavior
    ): void {
        if ($original->hasExtension(OrderPickingExtension::KEY)) {
            /* @var $extension OrderPickingExtension */
            $extension = $original->getExtension(OrderPickingExtension::KEY);

            $data->set('options', $extension->getOptions());
            $data->set('billing', $extension->getBilling());
            $data->set('shipping', $extension->getShipping());
            $data->set('hasPickingLists', true);
        }
    }
}
