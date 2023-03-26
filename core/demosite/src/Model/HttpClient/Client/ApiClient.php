<?php

namespace App\Model\HttpClient\Client;

use App\Model\HttpClient\GuzzleTrait;
use App\Model\HttpClient\ParseResponseTrait;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use RuntimeException;

class ApiClient implements ApiClientInterface
{
    use GuzzleTrait;
    use ParseResponseTrait;

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return array<int|string, mixed>
     */
    /**
     * @param array<string, mixed> $options
     * @return array<int|string, mixed>
     * @throws GuzzleException
     */
    public function request(string $method, string $uri, array $options = []): array
    {
        $client = $this->getHttpClient();
        try {
            $response = $client->request($method, $uri, $options);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $message = sprintf('Возникла ошибка [%s]. Код ответа: [%d]', $response->getBody()->getContents(),
                $response->getStatusCode());
            throw new BadResponseException($message, $e->getRequest(), $response, $e);
        }

        try {
            return $this->parseResponse($response);
        } catch (JsonException | Exception $e) {
            $message = sprintf('Возникла ошибка при попытке чтения ответа: %s. Тело ответа: %s', $e->getMessage(),
                $response->getBody()->getContents());
            throw new RuntimeException($message, $e->getCode(), $e);
        }

    }
}