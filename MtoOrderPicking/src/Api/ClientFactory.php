<?php

namespace MtoOrderPicking\Api;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class ClientFactory implements ClientFactoryInterface
{
    public const API_BASE_URI = 'MtoOrderPicking.config.apiBaseUri';

    public function __construct(protected SystemConfigService $configService, protected iterable $clientExtensions)
    {
    }

    public function create(array $config = []): ClientInterface
    {
        $baseUri = $this->configService->get(self::API_BASE_URI) ?? '';

        $client = new Client($baseUri);

        foreach ($this->clientExtensions as $clientExtension) {
            $client->addExtension($clientExtension);
        }

        return $client;
    }
}
