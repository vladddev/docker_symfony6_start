<?php

namespace App\Service;

use App\Entity\User;
use App\Event\User\RegisterEvent;
use App\Event\User\UserRemovedEvent;
use App\Event\User\UserUpdatedEvent;
use App\Exception\User\UserNotFoundException;
use App\Repository\UserRepository;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use JsonException;
use Redis;
use RedisException;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UserManager
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param Redis $redis
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EventDispatcherInterface $dispatcher
     * @param Security $security
     */
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private Redis                       $redis,
        private UserPasswordHasherInterface $passwordHasher,
        private EventDispatcherInterface    $dispatcher,
        private Security                    $security,
    )
    {
    }

    public function findOrFail(int $id): User
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->find($id) ?? throw new RuntimeException('Отсутствует такой пользователь');
    }

    /**
     * @param string $email
     * @return User
     * @throws NonUniqueResultException
     * @throws UserNotFoundException
     */
    public function findByEmailOrFail(string $email): User
    {
        $user = $this->getUserRepository()->findByEmail($email);
        if ($user === null) {
            throw new UserNotFoundException(sprintf('Не найден пользователь с email %s', $email));
        }
        return $user;
    }

    /**
     * @return UserRepository
     */
    private function getUserRepository(): UserRepository
    {
        return $this->entityManager->getRepository(User::class);
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function setEncodedPassword(User $user, string $password): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->eraseCredentials();
    }

    /**
     * @param User $user
     */
    public function registerUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->dispatcher->dispatch(new RegisterEvent($user));
    }

    /**
     * @param User $user
     */
    public function updateUser(User $user): void
    {
        $this->entityManager->flush();
        $this->dispatcher->dispatch(new UserUpdatedEvent($user));
    }

    /**
     * @param User $user
     */
    public function remove(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->dispatcher->dispatch(new UserRemovedEvent($user));
    }

    public function getCurrentUser(): User
    {
        $user = $this->security->getUser();
        if (!($user instanceof User)) {
            throw new AccessDeniedHttpException();
        }

        return $user;
    }


    /**
     * @param string $requestBody
     * @return Response|null
     * @throws JsonException|RedisException
     */
    public function getLoginBlockResponse(string $requestBody): ?Response
    {
        $response = null;
        $data = json_decode($requestBody, true, 512, JSON_THROW_ON_ERROR);
        $email = $data['email'];
        $code = Response::HTTP_BAD_REQUEST;

        $key = $this->getLoginBlockKey($email);
        $failedCount = $this->redis->get($key);
        if ($failedCount > 0) {
            $ttl = $this->redis->ttl($key);
            $response = new JsonResponse([
                'code' => $code,
                'message' => sprintf('Аккаунт заблокирован на %d секунд из-за %d последовательных неудачных попыток входа в систему',
                    $ttl, $failedCount),
            ], $code);
        }

        return $response;
    }

    public function getLoginBlockKey(string $email): string
    {
        return 'login:block:' . $email;
    }
}