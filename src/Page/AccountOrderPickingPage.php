<?php

namespace MtoOrderPicking\Page;

use MtoOrderPicking\Api\Model\CustomerOrderPicking;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Page;

class AccountOrderPickingPage extends Page
{
    protected ?CustomerOrderPicking $customerOrderPicking = null;

    protected ?SalesChannelContext $salesChannelContext = null;

    public function setSalesChannelContext(SalesChannelContext $salesChannelContext): void
    {
        $this->salesChannelContext = $salesChannelContext;
    }

    public function getCustomerOrderPicking(): ?CustomerOrderPicking
    {
        return $this->customerOrderPicking;
    }

    public function setCustomerOrderPicking(?CustomerOrderPicking $customerOrderPicking): void
    {
        $this->customerOrderPicking = $customerOrderPicking;
    }

    public function getMinDate(): \DateTime
    {
        $minDate = (new \DateTime())->add((new \DateInterval('P2D')));

        return $minDate;
    }

    public function getShowGrossPrice(): bool
    {
        return $this->salesChannelContext->getCurrentCustomerGroup()->getDisplayGross();
    }
}