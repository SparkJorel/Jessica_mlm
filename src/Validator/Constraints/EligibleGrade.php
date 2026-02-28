<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class EligibleGrade extends Constraint
{
    public $message = "Le grade doit être renseigné lorsque la promo est soumise à condition";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
