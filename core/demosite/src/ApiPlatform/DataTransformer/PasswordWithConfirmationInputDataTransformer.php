<?php


namespace App\ApiPlatform\DataTransformer;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\PasswordWithConfirmationInput;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class PasswordWithConfirmationInputDataTransformer implements DataTransformerInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @param PasswordWithConfirmationInput $object
     * @param string $to
     * @param array<string, mixed> $context
     * @return User|object
     */
    public function transform($object, string $to, array $context = [])
    {
        $this->validator->validate($object);

        /** @var User $user */
        $user = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? throw new NotFoundHttpException();

        $user->setPlainPassword($object->password);

        return $user;
    }

    /**
     * @param array<string, mixed>|object $data
     * @param string $to
     * @param array<string, mixed> $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to && PasswordWithConfirmationInput::class === ($context['input']['class'] ?? null);
    }
}