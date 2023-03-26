<?php


namespace App\ApiPlatform\OpenApiDecorator;

use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;

final class UserDecorator extends AbstractDecorator
{
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        Schemas::addTokenSchema($schemas);
        Schemas::addCredentialsSchema($schemas);
        Schemas::addViolationsSchema($schemas);
        Schemas::addSimpleStatusSchema($schemas);
        Schemas::addProblemSchema($schemas);

        $this->addRegistrationEndpoint($openApi);
        $this->addMeEndpoint($openApi);
        return $openApi;
    }

    /**
     * @param OpenApi $openApi
     */
    private function addRegistrationEndpoint(OpenApi $openApi): void
    {
        $callback = static function (Model\Operation $operation) {
            return $operation->withResponses([
                '200' => [
                    'description' => 'User resource created',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Token',
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
                '422' => [
                    'description' => 'Unprocessable Entity',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Violations',
                            ],
                        ],
                    ],
                ]

            ])->withRequestBody((new Model\RequestBody(
                description: 'Sing up request',
                content: (new ArrayObject([
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/User-user.create',
                        ],
                    ],
                    'application/ld+json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/User.jsonld-user.create',
                        ],
                    ],
                ])),
                required: true
            )));
        };
        $this->changeOperation($openApi, 'api_users_post_collection', self::METHOD_POST, $callback);
    }

    /**
     * @param OpenApi $openApi
     */
    private function addMeEndpoint(OpenApi $openApi): void
    {
        $callback = static function (Model\Operation $operation) {
            return $operation
                ->withSummary('Authenticated User resource.')
                ->withDescription('Retrieves authenticated User resource.')
                ->withResponses([
                    '200' => [
                        'description' => 'Authenticated User resource',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/User-user.read',
                                ],
                            ],
                            'application/ld+json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/User.jsonld-user.read',
                                ],
                            ],
                        ],
                    ],
                ]);
        };
        $this->changeOperation($openApi, 'api_users_me_collection', self::METHOD_GET, $callback);
    }
}