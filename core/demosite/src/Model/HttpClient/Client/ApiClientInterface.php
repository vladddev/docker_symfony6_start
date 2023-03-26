<?php

namespace App\Model\HttpClient\Client;

use GuzzleHttp\Exception\BadResponseException;
use JsonException;

interface ApiClientInterface
{
    public const METHOD_GET = 'get';
    public const METHOD_POST = 'post';

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     * @throws BadResponseException|JsonException
     */
    public function request(string $method, string $uri, array $options = []): array;
}