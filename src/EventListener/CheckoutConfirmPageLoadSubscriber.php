<?php

namespace MtoOrderPicking\EventListener;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CheckoutConfirmPageLoadSubscriber implements EventSubscriberInterface
{


    public function __construct(protected SystemConfigService $configService)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutConfirmPageLoadedEvent::class => 'onCheckoutConfigPageLoaded'
        ];
    }

    protected function applicable(Cart $cart): bool
    {
        $applicable = false;

        foreach ($cart->getLineItems() as $lineItem) {
            $payload = $lineItem->getPayload();

            if (array_key_exists('pickingListNumber', $payload)) {
                $applicable = true;
                break;
            }
        }

        return $applicable;
    }

    public function onCheckoutConfigPageLoaded(CheckoutConfirmPageLoadedEvent $event): void
    {
        $allowedPaymentMethods = $this->configService->get('MtoOrderPicking.config.paymentMethods');

        if (! $allowedPaymentMethods || count($allowedPaymentMethods) === 0 || ! $this->applicable($event->getPage()->getCart())) {
            return;
        }

        $paymentMethods = $event->getPage()->getPaymentMethods();

        $filteredPaymentMethods = $paymentMethods->filter(function (PaymentMethodEntity $paymentMethodEntity) use (
            $allowedPaymentMethods
        ) {
            return in_array($paymentMethodEntity->getId(), $allowedPaymentMethods);
        });

        $event->getPage()->setPaymentMethods($filteredPaymentMethods);
    }

}