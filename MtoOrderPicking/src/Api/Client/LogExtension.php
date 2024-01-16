<?php

namespace MtoOrderPicking\Api\Client;

use MtoOrderPicking\Api\ClientExtensionInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class LogExtension implements ClientExtensionInterface
{
    public function __construct(protected LoggerInterface $logger)
    {
    }

    public function beforeRequest(string $endpoint, string $method, array &$options): void
    {
        $this->logger->debug(
            'API: before request',
            ['endpoint' => $endpoint, 'method' => $method, 'options' => $options]
        );
    }

    public function onError(
        string $endpoint,
        string $method,
        array $options,
        ?ClientExceptionInterface $exception
    ): void {
        $this->logger->debug(
            'API: error',
            ['endpoint' => $endpoint, 'method' => $method, 'response' => $exception->getMessage()]
        );
    }

    public function afterRequest(string $endpoint, string $method, array $options, ?ResponseInterface $response): void
    {
        $this->logger->debug(
            'API: after request',
            [
                'endpoint' => $endpoint,
                'method' => $method,
                'response' => $response->getBody()->read($response->getBody()->getSize())
            ]
        );
    }

}