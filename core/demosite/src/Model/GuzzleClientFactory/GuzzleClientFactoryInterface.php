<?php

namespace App\Model\GuzzleClientFactory;

use GuzzleHttp\Client;

interface GuzzleClientFactoryInterface
{
    /**
     * @param array<string, string> $config
     * @return Client
     */
    public function create(array $config = []): Client;
}