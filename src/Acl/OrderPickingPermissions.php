<?php

namespace MtoOrderPicking\Acl;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class OrderPickingPermissions
{

    public function __construct(protected SystemConfigService $configService)
    {
    }

    public function allowed(CustomerEntity $entity): bool
    {
        $customerGroupIds = $this->configService->get('MtoOrderPicking.config.customerGroups');

        return in_array($entity->getGroupId(), $customerGroupIds);
    }
}