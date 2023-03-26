<?php declare(strict_types=1);

namespace App\Dto;

use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordWithConfirmationInput
{
    #[Assert\NotBlank(message: 'constraints.password.empty')]
    #[Assert\Length(
        min: User::MIN_PASSWORD_LENGTH,
        max: User::MAX_PASSWORD_LENGTH,
        minMessage: 'constraints.password.min',
        maxMessage: 'constraints.password.max',
    )]
    #[Groups(['password-with-confirmation'])]
    public string $password;

    #[Assert\NotBlank]
    #[Assert\Expression(
        'this.password == value',
        message: 'constraints.password.not_match'
    )]
    #[Groups(['password-with-confirmation'])]
    public string $passwordConfirmation;
}
