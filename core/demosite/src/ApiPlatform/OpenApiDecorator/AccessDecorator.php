<?php

namespace App\ApiPlatform\OpenApiDecorator;

use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;

class AccessDecorator extends AbstractDecorator
{

    /**
     * @inheritDoc
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $this->addInstancesEndpoint($openApi);
        return $openApi;
    }

    /**
     * @param OpenApi $openApi
     */
    private function addInstancesEndpoint(OpenApi $openApi): void
    {
        $callback = static function (Model\Operation $operation) {
            return $operation
                ->withSummary('Instances by Access')
                ->withDescription('Retrieves collection of instances')->withResponses([
                    '200' => [
                        'description' => 'Collection of instances',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Instance-access.instances.read',
                                ],
                            ],
                            'application/ld+json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Instance.jsonld-access.instances.read',
                                ],
                            ],
                        ],
                    ],
                    '400' => [
                        'description' => 'Invalid input',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Violations',
                                ],
                            ],
                        ],
                    ],
                ]);
        };
        $this->changeOperation($openApi, 'api_accesses_instances_item', self::METHOD_GET, $callback);
    }
}