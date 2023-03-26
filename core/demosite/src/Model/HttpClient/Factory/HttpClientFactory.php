<?php

namespace App\Model\HttpClient\Factory;

use App\Model\GuzzleClientFactory\GuzzleClientFactoryInterface;
use App\Model\HttpClient\Client\ApiClient;
use App\Model\HttpClient\Client\ApiClientInterface;
use GuzzleHttp\Client;

class HttpClientFactory implements HttpClientFactoryInterface
{
    public function __construct(
        protected GuzzleClientFactoryInterface $clientFactory
    )
    {
    }

    /**
     * @param array<string, mixed> $config
     * @return ApiClientInterface
     */
    public function createApiClient(array $config = []): ApiClientInterface
    {
        return (new ApiClient())
            ->setHttpClient($this->getHttpClient($config));
    }

    /**
     * @param array<string, mixed> $config
     * @return Client
     */
    protected function getHttpClient(array $config = []): Client
    {
        return $this->clientFactory->create($config);
    }
}