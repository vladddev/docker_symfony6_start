<?php


namespace App\Model\HttpClient;


use App\Helper\ParseHelper;
use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait ParseResponseTrait
{
    /**
     * Parses the response according to its content-type header.
     *
     * @param ResponseInterface $response
     * @param bool $allowErrorCode
     * @return array<int|string, mixed>
     * @throws JsonException
     * @throws Exception
     */
    protected function parseResponse(ResponseInterface $response, bool $allowErrorCode = false): array
    {
        $content = (string)$response->getBody();
        if (!$allowErrorCode && $response->getStatusCode() > 204) {
            throw new BadRequestHttpException('Код ответа:' . $response->getStatusCode() . '. Тело ответа: ' . $content);
        }

        if ('' === $content) {
            return [];
        }

        return ParseHelper::parse($content, $this->getContentType($response));
    }

    /**
     * Returns the content type header of a response.
     *
     * @param ResponseInterface $response
     * @return string Semi-colon separated join of content-type headers.
     */
    protected function getContentType(ResponseInterface $response): string
    {
        return implode(';', (array)$response->getHeader('content-type'));
    }
}