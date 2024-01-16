<?php

namespace MtoOrderPicking\Api\Model;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Struct\Struct;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

class CustomerOrderPicking extends Struct
{
    #[Ignore]
    protected $extensions = [];
    /**
     * @var BillingAddress[]
     */
    #[SerializedName("billingAddr")]
    protected array $billingAddresses = [];

    /**
     * @var array<ShippingAddress>|ShippingAddress[]
     */
    #[SerializedName("shippingAddr")]
    protected array $shippingAddresses = [];

    /**
     * @var array<PickingList>|PickingList[]
     */
    protected array $pickingLists = [];

    protected array $shopProducts = [];

    public function getBillingAddresses(): array
    {
        return $this->billingAddresses;
    }

    public function setBillingAddresses(array $billingAddresses): void
    {
        $this->billingAddresses = $billingAddresses;
    }

    public function getShippingAddresses(): array
    {
        return $this->shippingAddresses;
    }

    public function setShippingAddresses(array $shippingAddresses): void
    {
        $this->shippingAddresses = $shippingAddresses;
    }


    public function getPickingLists(): array
    {
        return $this->pickingLists;
    }

    /**
     * @var array<BillingAddress> $pickingLists
     */
    public function setPickingLists(array $pickingLists): void
    {
        $pickingListNumbers = array_map(
            fn(PickingList $pickingList) => $pickingList->getNumber(),
            $pickingLists
        );
        $this->pickingLists = array_combine($pickingListNumbers, $pickingLists);
    }

    public function getShopProducts(): array
    {
        return $this->shopProducts;
    }

    /**
     * @param  array|ProductEntity[]  $shopProducts
     * @return void
     */
    public function setShopProducts(array $shopProducts): void
    {
        foreach ($shopProducts as $product) {
            $this->shopProducts[$product->getProductNumber()] = $product;
        }
    }
}