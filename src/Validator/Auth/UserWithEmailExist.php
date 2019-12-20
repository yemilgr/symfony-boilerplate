<?php

namespace App\Validator\Auth;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserWithEmailExist extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The user with email "{{ value }}" does not exist.';
}
