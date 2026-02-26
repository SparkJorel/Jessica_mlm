<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * Class ConformStartedAtDatePromotion
 * @package App\Validator\Constraints
 */
class ConformStartedAtDatePromotion extends Constraint
{
    public $message = "La date de debut {{ started_at }} doit correspondre 
                        à une date de début de cycle";
}
