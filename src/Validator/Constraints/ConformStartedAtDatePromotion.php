<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ConformStartedAtDatePromotion extends Constraint
{
    public $message = "La date de debut {{ started_at }} doit correspondre 
                        à une date de début de cycle";
}
