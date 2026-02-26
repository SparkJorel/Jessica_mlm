<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * Class StartedAtLessThanEndedAt
 * @package App\Validator\Constraints
 */
class StartedAtLessThanEndedAt extends Constraint
{
    public $message = "La date de début {{ started_at }} doit être inférieure à la date de fin {{ ended_at }}";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
