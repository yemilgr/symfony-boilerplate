<?php

namespace App\Validator\Auth;

use App\Entity\PasswordReset;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ResetPasswordActive extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Please wait before retrying.';
}
