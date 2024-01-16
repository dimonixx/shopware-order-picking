<?php

namespace MtoOrderPicking\Api\Client;

use MtoOrderPicking\Api\ClientExtensionInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class AuthExtension implements ClientExtensionInterface
{
    public const API_TOKEN_CONFIG_PATH = 'MtoOrderPicking.config.apiToken';

    public function __construct(protected SystemConfigService $configService)
    {
    }

    public function beforeRequest(string $endpoint, string $method, array &$options): void
    {
        $token = $this->configService->get(self::API_TOKEN_CONFIG_PATH);

        if ($token) {
            if (! array_key_exists('headers', $options) || $options['headers']) {
                $options['headers'] = [];
            }

            $options['headers']['Authorization'] = sprintf('Bearer: %s', $token);
        }
    }

    public function onError(
        string $endpoint,
        string $method,
        array $options,
        ?ClientExceptionInterface $exception
    ): void {
        // TODO: Implement onError() method.
    }

    public function afterRequest(string $endpoint, string $method, array $options, ?ResponseInterface $response): void
    {

    }
}