<?php

namespace MtoOrderPicking\Page;

use MtoOrderPicking\Api\Model\CustomerOrderPicking;
use Shopware\Storefront\Page\Page;

class AccountOrderPickingPage extends Page
{
    protected ?CustomerOrderPicking $customerOrderPicking = null;

    public function getCustomerOrderPicking(): ?CustomerOrderPicking
    {
        return $this->customerOrderPicking;
    }

    public function setCustomerOrderPicking(?CustomerOrderPicking $customerOrderPicking): void
    {
        $this->customerOrderPicking = $customerOrderPicking;
    }
}