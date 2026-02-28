<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ConformEndedAtDatePromotion extends Constraint
{
    public $message = "La date de fin {{ ended_at }} doit correspondre 
                        à une fin de cycle";
}
