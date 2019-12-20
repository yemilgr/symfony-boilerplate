<?php

namespace App\Validator\Auth;

use App\Entity\PasswordReset;
use App\Repository\PasswordResetRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ResetPasswordActiveValidator extends ConstraintValidator
{
    /**
     * @var PasswordResetRepository
     */
    private $passwordResetRepository;

    public function __construct(PasswordResetRepository $passwordResetRepository)
    {
        $this->passwordResetRepository = $passwordResetRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\Auth\ResetPasswordActive */

        if (null === $value || '' === $value || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        /** @var PasswordReset $passwordReset */
        $passwordReset = $this->passwordResetRepository->findOneByUserEmail($value);

        if($passwordReset && $passwordReset->isInTtl()){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

}
