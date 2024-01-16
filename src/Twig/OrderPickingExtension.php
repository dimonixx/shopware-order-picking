<?php

namespace MtoOrderPicking\Twig;

use MtoOrderPicking\Acl\OrderPickingPermissions;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OrderPickingExtension extends AbstractExtension
{
    public function __construct(protected OrderPickingPermissions $orderPickingPermissions)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('mto_order_picking_allowed', [$this, 'orderPickingAllowed'])
        ];
    }

    public function orderPickingAllowed(CustomerEntity $customerEntity): bool
    {
        return $this->orderPickingPermissions->allowed($customerEntity);
    }

}