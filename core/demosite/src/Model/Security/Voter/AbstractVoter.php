<?php

namespace App\Model\Security\Voter;

use App\Entity\User;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter
{
    public const ANONYMOUS_USER_NAME = 'anon.';

    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const CREATE = 'create';


    /**
     * AbstractVoter constructor.
     * @param Security $security
     */
    public function __construct(protected Security $security)
    {
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     * @noinspection PhpMissingParamTypeInspection
     */
    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, $this->getSupportedAttributes(), true)) {
            return false;
        }

        if (!$this->checkSubjectClass($subject)) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $subject
     * @return bool
     */
    abstract protected function checkSubjectClass(mixed $subject): bool;

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     * @noinspection PhpMissingParamTypeInspection
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $this->getUser($token);

        if (!empty($user) && !$user instanceof User) {
            return false;
        }

        //Admin can perform any action
        if ($user?->isAdmin()) {
            return true;
        }

        return match ($attribute) {
            self::VIEW => $this->haveViewPermissions($subject, $user),
            self::EDIT => $this->haveEditPermissions($subject, $user),
            self::CREATE => $this->haveCreatePermissions($subject, $user),
            default => throw new LogicException(sprintf('Wrong type (%s) of access to object of class %s',
                $attribute,
                get_class($subject))),
        };
    }

    /**
     * @param mixed $subject
     * @param User|null $user
     * @return bool
     */
    abstract protected function canView(mixed $subject, ?User $user): bool;

    /**
     * @param mixed $subject
     * @param User $user
     * @return bool
     */
    abstract protected function canEdit(mixed $subject, User $user): bool;

    /**
     * @param mixed $subject
     * @param User $user
     * @return bool
     */
    abstract protected function canCreate(mixed $subject, User $user): bool;

    /**
     * @param TokenInterface $token
     * @return object|null
     */
    protected function getUser(TokenInterface $token): object|null
    {
        $user = $token->getUser();

        if ($user?->getUserIdentifier() === self::ANONYMOUS_USER_NAME) {
            $user = null;
        }

        return $user;
    }

    /**
     * @param mixed $subject
     * @param User|null $user
     * @return bool
     */
    private function haveViewPermissions(mixed $subject, ?User $user): bool
    {
        return $this->haveEditPermissions($subject, $user) || $this->canView($subject, $user);
    }

    /**
     * @param mixed $subject
     * @param User|null $user
     * @return bool
     */
    private function haveEditPermissions(mixed $subject, ?User $user): bool
    {
        return $user !== null && $this->canEdit($subject, $user);
    }

    /**
     * @param mixed $subject
     * @param User|null $user
     * @return bool
     */
    private function haveCreatePermissions(mixed $subject, ?User $user): bool
    {
        return $user !== null && $this->canCreate($subject, $user);
    }

    /**
     * @return array<int|string, mixed>
     */
    protected function getSupportedAttributes(): array
    {
        return [self::VIEW, self::EDIT, self::CREATE];
    }

}