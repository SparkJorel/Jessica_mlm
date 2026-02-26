<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ConformEndedAtDatePromotionValidator extends ConstraintValidator
{
    public function validate($endedAt, Constraint $constraint)
    {
        if (!$constraint instanceof ConformEndedAtDatePromotion) {
            throw new UnexpectedTypeException($constraint, ConformEndedAtDatePromotion::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $endedAt) {
            return;
        }

        $endedAt = $endedAt->format('Y-m-d H:i:s');

        if (!preg_match('/^24-[0-9]{2}-[0-9]{4} 15:30:00$/', $endedAt, $matches)) {
            // the argument must be a string or an object implementing __toString()
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ ended_at }}', $endedAt)
                ->addViolation();
        }
    }
}
