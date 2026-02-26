<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class StartedAtLessThanEndedAtValidator extends ConstraintValidator
{
    public function validate($promoBonusSpecial, Constraint $constraint)
    {
        if (!$constraint instanceof StartedAtLessThanEndedAt) {
            throw new UnexpectedTypeException($constraint, StartedAtLessThanEndedAt::class);
        }

        if (null === $promoBonusSpecial->getStartedAt() ||
            null === $promoBonusSpecial->getEndedAt()) {
            return;
        }

        if ($promoBonusSpecial->getStartedAt() > $promoBonusSpecial->getEndedAt()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ started_at }}', $promoBonusSpecial->getStartedAt())
                ->setParameter('{{ ended_at }}', $promoBonusSpecial->getEndedAt())
                ->addViolation();
        }
    }
}
