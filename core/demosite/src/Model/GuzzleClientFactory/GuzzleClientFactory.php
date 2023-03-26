<?php

namespace App\Model\GuzzleClientFactory;

use GuzzleHttp\Client;

class GuzzleClientFactory implements GuzzleClientFactoryInterface
{
    public const TIMEOUT = 30;

    /**
     * @param array<string, string> $config
     * @return Client
     */
    public function create(array $config = []): Client
    {
        if (!isset($config['timeout'])) {
            $config['timeout'] = self::TIMEOUT;
        }

        return new Client($config);
    }
}