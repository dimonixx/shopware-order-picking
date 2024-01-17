<?php

namespace MtoOrderPicking\OrderPicking\Cart;

use MtoOrderPicking\Api\Provider\DataProviderInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class OrderPickingProcessor implements CartProcessorInterface
{
    public function __construct(
        protected DataProviderInterface $dataProvider,
        protected EntityRepository $customerAddressRepository,
        protected QuantityPriceCalculator $calculator
    ) {
    }

    public function process(
        CartDataCollection $data,
        Cart $original,
        Cart $toCalculate,
        SalesChannelContext $context,
        CartBehavior $behavior
    ): void {

        if (! $context->getCustomer()) {
            return;
        }

        if ($data->has('billing')) {
            $criteria = new Criteria([$data->get('billing')]);
            $criteria->addAssociations(['country', 'salutation']);

            $billingAddress = $this->customerAddressRepository
                ->search($criteria, $context->getContext())
                ->first();

            if ($billingAddress instanceof CustomerAddressEntity) {
                $context
                    ->getCustomer()
                    ->setActiveBillingAddress($billingAddress);
            }
        }

        if ($data->has('shipping')) {
            $criteria = new Criteria([$data->get('shipping')]);
            $criteria->addAssociations(['country', 'salutation']);

            $shippingAddress = $this->customerAddressRepository
                ->search($criteria, $context->getContext())
                ->first();

            if ($shippingAddress instanceof CustomerAddressEntity) {
                $context
                    ->getCustomer()
                    ->setActiveShippingAddress($shippingAddress);
            }
        }

        $this->processLineItemPrices($toCalculate, $context);
    }

    protected function processLineItemPrices(
        Cart $toCalculate,
        SalesChannelContext $context
    ): void {
        foreach ($toCalculate->getLineItems() as $lineItem) {
            $payload = $lineItem->getPayload();

            if (! array_key_exists('pickingListNumber', $payload)) {
                $price = (float) $payload['pickingListProductGrossPrice'];

                $priceDefinition = new QuantityPriceDefinition(
                    $price,
                    $lineItem->getPrice()->getTaxRules(),
                    $lineItem->getPrice()->getQuantity()
                );

                $lineItem->setPrice($this->calculator->calculate($priceDefinition, $context));
                $lineItem->setPriceDefinition($priceDefinition);
            }
        }
    }
}
