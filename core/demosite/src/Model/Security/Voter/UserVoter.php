<?php


namespace App\Model\Security\Voter;


use App\Entity\User;

class UserVoter extends AbstractVoter
{
    /**
     * @inheritDoc
     */
    protected function checkSubjectClass(mixed $subject): bool
    {
        return $subject instanceof User;
    }

    /**
     * @param mixed $subject
     * @param User|null $user
     * @return bool
     */
    protected function canView(mixed $subject, ?User $user): bool
    {
        if (null === $user) {
            return false;
        }

        return $subject === $user;
    }

    /**
     * @param mixed $subject
     * @param User $user
     * @return bool
     */
    protected function canEdit(mixed $subject, User $user): bool
    {
        return $subject === $user;
    }

    /**
     * @param mixed $subject
     * @param User $user
     * @return bool
     */
    protected function canCreate(mixed $subject, User $user): bool
    {
        return true;
    }
}