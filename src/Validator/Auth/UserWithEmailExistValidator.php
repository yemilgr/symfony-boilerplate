<?php

namespace App\Validator\Auth;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserWithEmailExistValidator extends ConstraintValidator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint UserWithEmailExist */

        if (null === $value || '' === $value || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $user = $this->userRepository->findOneBy(['email' => $value]);
        if(!$user){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
