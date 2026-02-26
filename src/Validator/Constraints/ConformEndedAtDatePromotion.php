<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * Class ConformEndedAtDatePromotion
 * @package App\Validator\Constraints
 */
class ConformEndedAtDatePromotion extends Constraint
{
    public $message = "La date de fin {{ ended_at }} doit correspondre 
                        à une fin de cycle";
}
