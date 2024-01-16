<?php

namespace MtoOrderPicking\Api;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

interface ClientExtensionInterface
{
    public function beforeRequest(string $endpoint, string $method, array &$options): void;

    public function onError(string $endpoint, string $method, array $options, ?ClientExceptionInterface $exception): void;

    public function afterRequest(string $endpoint, string $method, array $options, ?ResponseInterface $response): void;
}