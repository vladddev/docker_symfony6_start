<?php


namespace App\ApiPlatform\DataPersister;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use App\Service\UserManager;


class UserDataPersister implements DataPersisterInterface
{
    /**
     * @param UserManager $userManager
     */
    public function __construct(
        private UserManager $userManager,
    )
    {
    }

    /**
     * @param $data
     * @return bool
     */
    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $data
     * @return User
     */
    public function persist($data): User
    {
        $isNew = null === $data->getId();
        $this->setPassword($data);

        if ($isNew) {
            $this->userManager->registerUser($data);
        } else {
            $this->userManager->updateUser($data);
        }

        return $data;
    }

    /**
     * @param User $user
     */
    private function setPassword(User $user): void
    {
        if (null !== $user->getPlainPassword()) {
            $this->userManager->setEncodedPassword($user, $user->getPlainPassword());
        }
    }

    public function remove($data): void
    {
        $this->userManager->remove($data);
    }
}