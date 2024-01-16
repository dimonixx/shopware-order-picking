<?php

namespace MtoOrderPicking\Api;

interface ClientFactoryInterface
{
    public function create(array $config = []): ClientInterface;
}