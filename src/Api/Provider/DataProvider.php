<?php

namespace MtoOrderPicking\Api\Provider;

use MtoOrderPicking\Api\ClientFactoryInterface;
use MtoOrderPicking\Api\ClientInterface;
use MtoOrderPicking\Api\Model\CustomerOrderPicking;
use MtoOrderPicking\Api\Model\PickingList;
use MtoOrderPicking\Api\Model\PickingListProduct;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class DataProvider implements DataProviderInterface
{
    public const CONFIG_PATH_ORDER_PICKING_LISTS = 'MtoOrderPicking.config.apiEndpointPickingLists';

    protected ?ClientInterface $client = null;

    protected array $customerOrderPicking = [];

    public function __construct(
        protected ClientFactoryInterface $clientFactory,
        protected SerializerInterface $serializer,
        protected EntityRepository $productRepository,
        protected SystemConfigService $configService
    ) {
    }

    protected function getClient(): ClientInterface
    {
        if (! $this->client) {
            $this->client = $this->clientFactory->create();
        }

        return $this->client;
    }

    protected function prepareURI(string $uri, array $parameters): string
    {
        foreach ($parameters as $parameter => $value) {
            $uri = preg_replace('/\{#'.$parameter.'#\}/', $value, $uri);
        }

        return $uri;
    }

    public function getCustomerOrderPicking(SalesChannelContext $context): ?CustomerOrderPicking
    {
        $customerNumber = $context->getCustomer()->getCustomerNumber();

        if (array_key_exists($customerNumber, $this->customerOrderPicking)) {
            return $this->customerOrderPicking[$customerNumber];
        }

        $uri = $this->configService->get(self::CONFIG_PATH_ORDER_PICKING_LISTS);

        $orderPickingData = $this->getClient()
            ->request(
                $this->prepareURI($uri, ['userId' => $customerNumber]),
                Request::METHOD_GET,
                ['headers' => 'Content-Type: application/json']
            );

        $customerOrderPickingData = null;

        if ($orderPickingData) {
            $customerOrderPickingData = $this->serializer
                ->deserialize(
                    $orderPickingData,
                    CustomerOrderPicking::class,
                    'json',
                    ['disable_type_enforcement' => true]
                );

            $skus = [];

            foreach ($customerOrderPickingData->getPickingLists() as $pickingList) {
                $skus = array_merge($skus, array_map(
                    fn(PickingListProduct $product) => $product->getSwagSku(),
                    $pickingList->getProducts()
                ));
            }

            $productSearchCriteria = new Criteria();
            $productSearchCriteria->addFilter(
                new EqualsAnyFilter('productNumber', $skus)
            );
            $productSearchCriteria->addAssociations(['media', 'media.media']);

            $products = $this->productRepository
                ->search($productSearchCriteria, $context->getContext())
                ->getElements();

            $customerOrderPickingData->setShopProducts(array_values($products));
        }

        return $customerOrderPickingData;
    }

    public function getPickingList(string $pickingListNumber, SalesChannelContext $context): ?PickingList
    {
        $pickingLists = $this->getCustomerOrderPicking($context)->getPickingLists();

        return array_key_exists($pickingListNumber, $pickingLists) ? $pickingLists[$pickingListNumber] : null;
    }

    public function getProduct(string $productSku, SalesChannelContext $context): ?PickingListProduct
    {
        $pickingLists = $this->getCustomerOrderPicking($context)?->getPickingLists();

        $pickingListProduct = null;

        if ($pickingLists) {
            $pickingListProduct = array_reduce(
                $pickingLists,
                function ($searchedPickingListProduct, PickingList $pickingList) use ($productSku) {
                    foreach ($pickingList->getProducts() as $pickingListProduct) {
                        if (! $searchedPickingListProduct instanceof PickingListProduct &&
                            $productSku === $pickingListProduct->getSwagSku()) {
                            $searchedPickingListProduct = $pickingListProduct;
                        }
                    }

                    return $searchedPickingListProduct;
                }
            );
        }

        return $pickingListProduct;
    }
}
