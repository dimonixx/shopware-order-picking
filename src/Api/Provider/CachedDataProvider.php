<?php

namespace MtoOrderPicking\Api\Provider;

use MtoOrderPicking\Api\Model\CustomerOrderPicking;
use MtoOrderPicking\Api\Model\PickingListProduct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CachedDataProvider implements DataProviderInterface
{
    public const CACHE_TAG = 'picking-lists';

    public function __construct(protected DataProviderInterface $dataProvdier, protected AdapterInterface $cache)
    {
    }

    public function getCustomerOrderPicking(SalesChannelContext $context): ?CustomerOrderPicking
    {
        $cacheKey = $this->getCacheKey($context);

        $cacheItem = $this->cache->getItem($cacheKey);

        if (! $cacheItem->isHit()) {
            $pickingLists = $this->dataProvdier->getCustomerOrderPicking($context);

            $cacheItem->set($pickingLists);
            $cacheItem->tag(self::CACHE_TAG);

            $this->cache->save($cacheItem);
        }

        return $cacheItem->get();
    }

    /**
     * @param  string  $productSku
     * @param  SalesChannelContext  $context
     * @return PickingListProduct|null
     */
    public function getProduct(string $productSku, SalesChannelContext $context): ?PickingListProduct
    {
        return $this->dataProvdier->getProduct($productSku, $context);
    }

    private function getCacheKey(SalesChannelContext $context): string
    {
        return sprintf('picking_lists_%s_%s', $context->getSalesChannelId(), $context->getCustomerId());
    }
}
