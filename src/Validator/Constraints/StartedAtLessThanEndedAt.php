<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class StartedAtLessThanEndedAt extends Constraint
{
    public $message = "La date de début {{ started_at }} doit être inférieure à la date de fin {{ ended_at }}";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
