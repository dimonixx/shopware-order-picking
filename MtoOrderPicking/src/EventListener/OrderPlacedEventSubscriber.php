<?php

namespace MtoOrderPicking\EventListener;

use MtoOrderPicking\OrderExport\Messaging\OrderExportMessage;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderPlacedEventSubscriber implements EventSubscriberInterface
{
    public function __construct(protected MessageBusInterface $messageBus)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [CheckoutOrderPlacedEvent::class => 'onOrderPlaced'];
    }

    public function onOrderPlaced(CheckoutOrderPlacedEvent $event): void
    {
        $message = new OrderExportMessage($event->getContext(), $event->getOrderId());
        $this->messageBus->dispatch($message);
    }
}