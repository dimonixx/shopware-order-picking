<?php

namespace MtoOrderPicking\Acl;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class OrderPickingPermissions
{

    public function __construct(protected SystemConfigService $configService)
    {
    }

    public function allowed(?CustomerEntity $customer): bool
    {
        $customerGroupIds = $this->configService->get('MtoOrderPicking.config.customerGroups');

        return is_array($customerGroupIds) && $customer !== null && in_array($customer->getGroupId(), $customerGroupIds);
    }
}