<?php

namespace MtoOrderPicking\Api\Model;

use Shopware\Core\Framework\Struct\JsonSerializableTrait;
use Shopware\Core\Framework\Struct\Struct;
use Symfony\Component\Serializer\Annotation\SerializedName;

class PickingListProduct implements \JsonSerializable
{
    use JsonSerializableTrait;

    protected ?string $swagSku;

    #[SerializedName('qty')]
    protected ?string $quantity = null;

    protected ?string $netPrice;

    protected ?string $grossPrice;

    public function getSwagSku(): ?string
    {
        return $this->swagSku;
    }

    public function setSwagSku(?string $swagSku): void
    {
        $this->swagSku = $swagSku;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(?string $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getNetPrice(): ?string
    {
        return $this->netPrice;
    }

    public function setNetPrice(?string $netPrice): void
    {
        $this->netPrice = $netPrice;
    }

    public function getGrossPrice(): ?string
    {
        return $this->grossPrice;
    }

    public function setGrossPrice(?string $grossPrice): void
    {
        $this->grossPrice = $grossPrice;
    }
}