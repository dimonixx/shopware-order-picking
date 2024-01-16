<?php

namespace MtoOrderPicking\Api;

interface ClientInterface
{
    public function request(string $endpoint, string $method, array $options = []): ?string;

    public function getErrors(): array;
}