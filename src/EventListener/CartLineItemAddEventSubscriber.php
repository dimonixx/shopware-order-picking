<?php

namespace MtoOrderPicking\EventListener;

use MtoOrderPicking\Api\Model\PickingListProduct;
use MtoOrderPicking\Api\Provider\DataProviderInterface;
use Shopware\Core\Checkout\Cart\Event\AfterLineItemAddedEvent;
use Shopware\Core\Checkout\Cart\Event\BeforeLineItemAddedEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CartLineItemAddEventSubscriber implements EventSubscriberInterface
{

    public function __construct(
        protected EntityRepository $productRepository,
        protected DataProviderInterface $dataProvider,
        protected QuantityPriceCalculator $calculator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeLineItemAddedEvent::class => 'onBeforeLineItemAdded'
        ];
    }

    public function onBeforeLineItemAdded(BeforeLineItemAddedEvent $event): void
    {
        $lineItem = $event->getLineItem();
        $payload = $lineItem->getPayload();

        if (array_key_exists('pickingListNumber', $payload)) {
            return;
        }

        $product = $this->productRepository
            ->search(new Criteria([$lineItem->getReferencedId()]), $event->getContext())
            ->first();

        if ($product instanceof ProductEntity) {
            $pickingListProduct = $this->dataProvider
                ->getProduct($product->getProductNumber(), $event->getSalesChannelContext());

            if ($pickingListProduct instanceof PickingListProduct) {
                $payload['pickingListProductNetPrice'] = $pickingListProduct->getNetPrice();
                $payload['pickingListProductGrossPrice'] = $pickingListProduct->getGrossPrice();

                $lineItem->setPayload($payload);
            }
        }
    }
}
