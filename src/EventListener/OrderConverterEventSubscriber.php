<?php

namespace MtoOrderPicking\EventListener;

use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderConverterEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CartConvertedEvent::class => 'onCartConverted'
        ];
    }

    public function onCartConverted(CartConvertedEvent $event): void
    {
        $convertedData = $event->getConvertedCart();
        $cart = $event->getCart();

        $convertedData['customFields'] = $cart->getData()->get('options');

        $event->setConvertedCart($convertedData);
    }
}
