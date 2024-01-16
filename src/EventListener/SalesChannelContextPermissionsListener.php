<?php

namespace MtoOrderPicking\EventListener;

use Shopware\Core\Content\Product\Cart\ProductCartProcessor;
use Shopware\Core\System\SalesChannel\Event\SalesChannelContextCreatedEvent;
use Shopware\Core\System\SalesChannel\Event\SalesChannelContextPermissionsChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SalesChannelContextPermissionsListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SalesChannelContextCreatedEvent::class => 'onPermissions'
        ];
    }

    public function onPermissions(SalesChannelContextCreatedEvent $event): void
    {
        $permissions = $event->getSalesChannelContext()->getPermissions();
        $permissions[ProductCartProcessor::ALLOW_PRODUCT_PRICE_OVERWRITES] = true;
        $event->getSalesChannelContext()->setPermissions($permissions);
    }
}