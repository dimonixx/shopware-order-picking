<?php

namespace MtoOrderPicking\Tests;

use MtoOrderPicking\Api\Model\CustomerOrderPicking;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;

class TestTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testDeserialize(): void
    {
        $data = json_decode(file_get_contents(__DIR__.'/data/response.json'), true);

        $serialized = $this->getContainer()->get('serializer');

        $deserializedData = $serialized->deserialize(json_encode($data), CustomerOrderPicking::class, 'json');

        print_r($deserializedData);
    }
}