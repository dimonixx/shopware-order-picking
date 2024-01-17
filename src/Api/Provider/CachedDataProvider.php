<?php

namespace MtoOrderPicking\Api\Provider;

use MtoOrderPicking\Api\ClientFactoryInterface;
use MtoOrderPicking\Api\Model\CustomerOrderPicking;
use MtoOrderPicking\Api\Model\PickingList;
use MtoOrderPicking\Api\Model\PickingListProduct;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CachedDataProvider extends DataProvider implements DataProviderInterface
{
    public const CACHE_TAG = 'picking-lists';

    public function __construct(
        ClientFactoryInterface $clientFactory,
        SerializerInterface $serializer,
        EntityRepository $productRepository,
        SystemConfigService $configService,
        protected AdapterInterface $cache
    ) {
        parent::__construct($clientFactory, $serializer, $productRepository, $configService);
    }


    public function getCustomerOrderPicking(SalesChannelContext $context): ?CustomerOrderPicking
    {
        $cacheKey = $this->getCacheKey($context);

        $cacheItem = $this->cache->getItem($cacheKey);

        if (! $cacheItem->isHit()) {
            $pickingLists = parent::getCustomerOrderPicking($context);

            $cacheItem->set($pickingLists);
            $cacheItem->tag(self::CACHE_TAG);

            $this->cache->save($cacheItem);
        }

        return $cacheItem->get();
    }

    public function getPickingList(string $pickingListNumber, SalesChannelContext $context): ?PickingList
    {
        return parent::getPickingList($pickingListNumber, $context);
    }

    /**
     * @param  string  $productSku
     * @param  SalesChannelContext  $context
     * @return PickingListProduct|null
     */
    public function getProduct(string $productSku, SalesChannelContext $context): ?PickingListProduct
    {
        return parent::getProduct($productSku, $context);
    }

    private function getCacheKey(SalesChannelContext $context): string
    {
        return sprintf('picking_lists_%s_%s', $context->getSalesChannelId(), $context->getCustomerId());
    }
}
