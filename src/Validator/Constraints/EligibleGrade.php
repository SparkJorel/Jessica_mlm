<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * Class EligibleGrade
 * @package App\Validator\Constraints
 */
class EligibleGrade extends Constraint
{
    public $message = "Le grade doit être renseigné lorsque la promo est soumise à condition";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
