<?php

namespace MtoOrderPicking\Page;

use MtoOrderPicking\Api\Provider\DataProviderInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AccountOrderPickingPageLoader
{
    public function __construct(
        protected DataProviderInterface $dataProvider,
        protected GenericPageLoader $genericPageLoader,
        protected EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function load(Request $request, SalesChannelContext $context): AccountOrderPickingPage
    {
        $page = $this->genericPageLoader->load($request, $context);
        $page = AccountOrderPickingPage::createFrom($page);

        if ($context->getCustomer()) {
            $customerOrderPicking = $this->dataProvider
                ->getCustomerOrderPicking($context);

            $page->setCustomerOrderPicking($customerOrderPicking);
        }

        $page->setSalesChannelContext($context);

        return $page;
    }
}