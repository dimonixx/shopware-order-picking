<?php

namespace MtoOrderPicking\OrderPicking\Service;

use MtoOrderPicking\OrderPicking\Cart\OrderPickingExtension;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartPersister;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Checkout\Customer\SalesChannel\AccountService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class OrderPickingCartService
{
    public function __construct(
        protected LineItemFactoryRegistry $lineItemFactoryRegistry,
        protected CartService $cartService,
        protected AccountService $accountService,
        protected EntityRepository $productRepository,
        protected EntityRepository $customerAddressRepository,
        protected EntityRepository $countryAddressRepository,
        protected EntityRepository $salutationRepository
    ) {
    }

    public function addToCart(
        Cart $cart,
        SalesChannelContext $salesChannelContext,
        array $lineItems,
        ?array $addresses = null,
        ?array $options = null
    ): void {
        if (! $cart->hasExtension(OrderPickingExtension::KEY)) {
            $cart->addExtension(OrderPickingExtension::KEY, new OrderPickingExtension());
        }

        if (is_array($addresses)) {
            $this->setCartAddresses($cart, $salesChannelContext, $addresses);
        }

        if (is_array($options)) {
            $this->setCartOptions($cart, $salesChannelContext, $options);
        }

        foreach ($lineItems as $lineItemData) {
            $lineItem = $this->createOrFindLineItem($cart, $lineItemData, $salesChannelContext);

            if ($lineItem->isModified()) {
                $this->cartService->add($cart, [$lineItem], $salesChannelContext);
            } else {
                $this->cartService->update(
                    $cart,
                    [['id' => $lineItem->getId(), 'quantity' => (int) $lineItemData['quantity']]],
                    $salesChannelContext
                );
            }
        }
    }

    protected function createOrFindLineItem(
        Cart $cart,
        array $lineItemData,
        SalesChannelContext $salesChannelContext
    ): LineItem {
        $existingLineItems = $cart->getLineItems()->filter(function (LineItem $lineItem) use ($lineItemData) {
            $payload = $lineItem->getPayload();

            return $lineItem->getReferencedId() === $lineItemData['referencedId'] &&
                array_key_exists('pickingListNumber', $payload) &&
                $payload['pickingListNumber'] === $lineItemData['pickingListNumber'];
        });

        $lineItem = $existingLineItems->first();

        $quantity = (int) $lineItemData['quantity'];
        $price = $lineItemData['netPrice'];

        if (! $lineItem instanceof LineItem) {
            $lineItem = $this->lineItemFactoryRegistry->create(
                [
                    'type' => LineItem::PRODUCT_LINE_ITEM_TYPE,
                    'referencedId' => $lineItemData['referencedId'],
                    'quantity' => (int) $lineItemData['quantity'],
                    'payload' => ['pickingListNumber' => $lineItemData['pickingListNumber']],
                    'priceDefinition' => [
                        'price' => (float) $price,
                        'quantity' => $quantity,
                        'isCalculated' => true,
                        'type' => QuantityPriceDefinition::TYPE,
                        'taxRules' => [
                            [
                                'taxRate' => 19, //TODO: get tax rate from shopware product
                                'percentage' => 100,
                            ]
                        ]
                    ]
                ],
                $salesChannelContext
            );

            $lineItem->setStackable(true);
            $lineItem->setRemovable(true);
        }

        return $lineItem;
    }

    protected function setCartAddresses(Cart $cart, SalesChannelContext $salesChannelContext, array $addresses): void
    {
        ['billing' => $billing, 'shipping' => $shipping] = $addresses;

        $billing = json_decode($billing, true);
        $shipping = json_decode($shipping, true);

        $billingAddressId = $this->findOrCreateAddress($billing, $salesChannelContext);
        $shippingAddressId = $this->findOrCreateAddress($shipping, $salesChannelContext);

        /* @var $orderPickingExtension OrderPickingExtension */
        $orderPickingExtension = $cart->getExtension(OrderPickingExtension::KEY);
        $orderPickingExtension->setBilling($billingAddressId);
        $orderPickingExtension->setShipping($shippingAddressId);
    }

    protected function setCartOptions(Cart $cart, SalesChannelContext $salesChannelContext, array $options): void
    {
        /* @var $orderPickingExtension OrderPickingExtension */
        $orderPickingExtension = $cart->getExtension(OrderPickingExtension::KEY);
        $orderPickingExtension->setOptions($options);
    }

    protected function findOrCreateAddress(array $addressData, SalesChannelContext $salesChannelContext)
    {
        $addressSearchCriteria = new Criteria();
        $addressSearchCriteria->addAssociation('customer')
            ->addAssociation('country')
            ->addFilter(
                new EqualsFilter('firstName', $addressData['firstName']),
                new EqualsFilter('lastName', $addressData['lastName']),
                new EqualsFilter('street', $addressData['street']),
                new EqualsFilter('city', $addressData['city']),
                new EqualsFilter('zipcode', $addressData['zip']),
                new EqualsFilter('customer.id', $salesChannelContext->getCustomerId()),
                new EqualsFilter('country.iso', $addressData['country'])
            );

        $addressSearchResult = $this->customerAddressRepository
            ->searchIds($addressSearchCriteria, $salesChannelContext->getContext());

        $addressId = $addressSearchResult->firstId();

        if (! $addressId) {
            $countrySearchCriteria = new Criteria();
            $countrySearchCriteria->addFilter(new EqualsFilter('iso', $addressData['country']));
            $countryId = $this->countryAddressRepository
                ->searchIds($countrySearchCriteria, $salesChannelContext->getContext())
                ->firstId();

            $salutationCriteria = new Criteria();
            $salutationCriteria->addFilter(new EqualsFilter('salutationKey', 'not_specified'));

            $salutationId = $this->salutationRepository
                ->searchIds($salutationCriteria, $salesChannelContext->getContext())
                ->firstId();

            $addressData['zipcode'] = $addressData['zip'];
            $addressData['customer'] = ['id' => $salesChannelContext->getCustomerId()];
            $addressData['salutation'] = ['id' => $salutationId];
            $addressData['country'] = ['id' => $countryId];


            $event = $this->customerAddressRepository->create([$addressData], $salesChannelContext->getContext());
            $events = $event->getEvents()->getElements();

            foreach ($events as $event) {
                if ($event instanceof EntityWrittenEvent && $event->getEntityName() === 'customer_address') {
                    $ids = $event->getIds();

                    $addressId = reset($ids);
                }
            }
        }

        return $addressId;
    }
}