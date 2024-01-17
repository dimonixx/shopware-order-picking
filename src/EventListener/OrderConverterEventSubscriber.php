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

        $options = array_filter($cart->getData()->get('options'));

        if (array_key_exists('delivery_date', $options) && array_key_exists('fix', $options)) {
            [$hours, $minutes] = explode(':', $options['fix']);

            $options['fix'] = (new \DateTime($options['delivery_date']))
                ->setTime($hours, $minutes)
                ->format(\DateTime::RFC3339);
        }

        $convertedData['customFields'] = $options;


        $event->setConvertedCart($convertedData);
    }
}
