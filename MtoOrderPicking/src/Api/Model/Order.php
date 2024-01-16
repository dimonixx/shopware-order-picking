<?php

namespace MtoOrderPicking\Api\Model;

use Shopware\Core\Checkout\Order\OrderEntity;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Order
{
    public const DEFAULT_PICKING_LIST = '-1';
    protected ?BillingAddress $billingAddress = null;

    protected ?ShippingAddress $shippingAddress = null;

    /**
     * @var array<PickingList>|PickingList[]
     */
    protected array $pickingLists = [];

    protected ?OrderAdditional $additional = null;

    public function __construct(OrderEntity $orderEntity)
    {
        $this->billingAddress = BillingAddress::fromOrderAddressEntity($orderEntity->getBillingAddress());
        $this->shippingAddress = ShippingAddress::fromOrderAddressEntity(
            $orderEntity->getDeliveries()->first()->getShippingOrderAddress()
        );

        foreach ($orderEntity->getLineItems() as $lineItem) {
            $payload = $lineItem->getPayload();

            $pickingListNumber = array_key_exists('pickingListNumber', $payload) ?
                $payload['pickingListNumber'] :
                self::DEFAULT_PICKING_LIST;

            if (! array_key_exists($pickingListNumber, $this->pickingLists)) {
                $this->pickingLists[$pickingListNumber] = new PickingList();
            }

            $pickingListProduct = new PickingListProduct();
            $pickingListProduct->setSwagSku($lineItem->getProduct()->getProductNumber());
            $pickingListProduct->setQuantity($lineItem->getQuantity());

            $this->pickingLists[$pickingListNumber]->addProduct($pickingListProduct);
        }

        $orderAdditional = $orderEntity->getCustomFieldsValues(
            'customer',
            'phone',
            'notes',
            'delivery_date',
            'fix',
            'neutral',
            'avis'
        );

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $this->additional = new OrderAdditional();
        foreach ($orderAdditional as $key => $value) {
            $propertyAccessor->setValue($this->additional, $key, $value);
        }
    }

    public function getBillingAddress(): ?BillingAddress
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?BillingAddress $billingAddress): void
    {
        $this->billingAddress = $billingAddress;
    }

    public function getShippingAddress(): ?ShippingAddress
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?ShippingAddress $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function getPickingLists(): array
    {
        return $this->pickingLists;
    }

    public function setPickingLists(array $pickingLists): void
    {
        $this->pickingLists = $pickingLists;
    }

    public function getAdditional(): ?OrderAdditional
    {
        return $this->additional;
    }

    public function setAdditional(?OrderAdditional $additional): void
    {
        $this->additional = $additional;
    }
}
