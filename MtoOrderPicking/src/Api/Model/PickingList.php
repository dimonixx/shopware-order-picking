<?php

namespace MtoOrderPicking\Api\Model;

use Shopware\Core\Framework\Struct\Struct;

class PickingList
{
    protected ?string $number;

    protected ?string $name;

    /**
     * @var PickingListProduct[]|array<PickingListProduct>
     */
    protected array $products = [];

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): void
    {
        $this->number = $number;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array<PickingListProduct>
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param  array<PickingListProduct>  $products
     * @return void
     */
    public function setProducts(array $products): void
    {
        $this->products = $products;
    }

    public function addProduct(PickingListProduct $product): void
    {
        $this->products[] = $product;
    }
}
