<?php

namespace App\Model\HttpClient\Factory;

use App\Model\HttpClient\Client\ApiClientInterface;

interface HttpClientFactoryInterface
{
    /**
     * @param array<string, mixed> $config
     * @return ApiClientInterface
     */
    public function createApiClient(array $config = []): ApiClientInterface;
}