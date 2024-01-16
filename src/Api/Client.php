<?php

namespace MtoOrderPicking\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class Client implements ClientInterface
{
    protected array $errors;

    protected GuzzleClientInterface $client;

    /**
     * @var array|ClientExtensionInterface[]
     */
    protected array $extensions = [];

    public function __construct(protected string $baseUri)
    {
        $this->client = new GuzzleClient(['base_uri' => $this->baseUri]);
    }

    public function addExtension(ClientExtensionInterface $extension): void
    {
        $this->extensions[] = $extension;
    }

    public function request(string $endpoint, string $method, array $options = []): ?string
    {
        $this->errors = [];

        try {
            foreach ($this->extensions as $extension) {
                $extension->beforeRequest($endpoint, $method, $options);
            }

            $response = $this->client->request($endpoint, $method, $options);

            foreach ($this->extensions as $extension) {
                $extension->afterRequest($endpoint, $method, $options, $response);
            }
        } catch (GuzzleException $exception) {
            foreach ($this->extensions as $extension) {
                $extension->onError($endpoint, $method, $options, $exception);
            }

            $this->errors[] = $exception->getMessage();

            $response = null;
        }

        return $response?->getBody()->read($response->getBody()->getSize());
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}