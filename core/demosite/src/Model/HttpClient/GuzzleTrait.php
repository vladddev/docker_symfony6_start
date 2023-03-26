<?php


namespace App\Model\HttpClient;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface as HttpClientInterface;

trait GuzzleTrait
{
    protected HttpClientInterface $httpClient;

    /**
     * Sets the HTTP client instance.
     *
     * @param HttpClientInterface $client
     * @return self
     */
    public function setHttpClient(HttpClientInterface $client): self
    {
        $this->httpClient = $client;

        return $this;
    }

    /**
     * Returns the HTTP client instance.
     *
     * @return HttpClientInterface
     */
    protected function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient ??= new Client();
    }
}