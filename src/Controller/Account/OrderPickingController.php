<?php declare(strict_types=1);

namespace MtoOrderPicking\Controller\Account;

use MtoOrderPicking\OrderPicking\Service\OrderPickingCartService;
use MtoOrderPicking\Page\AccountOrderPickingPageLoader;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class OrderPickingController extends StorefrontController
{
    public function __construct(
        protected AccountOrderPickingPageLoader $accountOrderPickingPageLoader,
        protected OrderPickingCartService $orderPickingCartService
    ) {
    }

    #[Route(path: '/account/order-picking', name: 'frontend.account.order-picking',defaults: ['_loginRequired' => true], methods: ['GET'])]
    public function orderPicking(Request $request, SalesChannelContext $context): Response
    {
        $page = $this->accountOrderPickingPageLoader->load($request, $context);

        return $this->renderStorefront(
            '@MtoOrderPicking/storefront/page/account/orderPicking.html.twig',
            ['page' => $page]
        );
    }

    #[Route(path: '/account/order-picking', name: 'frontend.account.order-picking-add-to-cart', defaults: ['XmlHttpRequest' => true, '_loginRequired' => true, '_loginRequiredAllowGuest' => true],methods: ['POST'])]
    public function addProductsToCart(
        Cart $cart,
        RequestDataBag $requestDataBag,
        Request $request,
        SalesChannelContext $context
    ): Response {
        $this->orderPickingCartService
            ->addToCart(
                $cart,
                $context,
                $requestDataBag->all('lineItems'),
                $requestDataBag->all('addresses'),
                $requestDataBag->all('options')
            );

        return $this->createActionResponse($request);
    }
}