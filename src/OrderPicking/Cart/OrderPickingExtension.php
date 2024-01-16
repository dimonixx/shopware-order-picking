<?php

namespace MtoOrderPicking\OrderPicking\Cart;

use Shopware\Core\Framework\Struct\Struct;

class OrderPickingExtension extends Struct
{
    final public const KEY = 'order-picking';

    protected ?string $billing = null;

    protected ?string $shipping = null;

    protected ?array $options = null;

    public function getBilling(): ?string
    {
        return $this->billing;
    }

    public function setBilling(?string $billing): void
    {
        $this->billing = $billing;
    }

    public function getShipping(): ?string
    {
        return $this->shipping;
    }

    public function setShipping(?string $shipping): void
    {
        $this->shipping = $shipping;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }
}
