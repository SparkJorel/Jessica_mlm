<?php

namespace App\Validator\Constraints;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UplinePositionValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function validate($user, Constraint $constraint)
    {
        if (!$constraint instanceof UplinePosition) {
            throw new UnexpectedTypeException($constraint, UplinePosition::class);
        }

        if (!is_string($constraint->errorPath)) {
            throw new UnexpectedTypeException($constraint->errorPath, 'string or null');
        }

        if (!$user instanceof User) {
            return;
        }

        if (!$user->getUpline()) {
            return;
        }

        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager->getRepository(User::class);

        $userResult = $repository->validateUserPosition($user->getUpline(), $user->getPosition());

        if (!$userResult) {
            return;
        }

        $newPosition = $this->getOppositeOfTheCurrentPosition($user->getPosition());

        $userResult = $repository->validateUserPosition($user->getUpline(), $newPosition);

        if (!$userResult) {
            $user->setPosition($newPosition);

            return;
        }

        $userResult = $repository->validateUserPosition($user->getUpline(), $user->getPosition());

        if ($userResult && $userResult instanceof User) {
            $user->setUpline($userResult);
            $userResult = $repository->validateUserPosition($user->getUpline(), $user->getPosition());

            if (!$userResult) {
                return;
            } else {
                $this->context->buildViolation($constraint->message)
                ->atPath($constraint->errorPath)
                ->setParameter('{{ side }}', $user->getPosition())
                ->setParameter('{{ username }}', $user->getUpline()->getUsername())
                ->setInvalidValue($user->getPosition())
                ->setCode(UplinePosition::POSITION_ALREADY_TAKEN)
                ->addViolation();
            }
        }

        $this->context->buildViolation($constraint->message)
            ->atPath($constraint->errorPath)
            ->setParameter('{{ side }}', $user->getPosition())
            ->setParameter('{{ username }}', $user->getUpline()->getusername())
            ->setInvalidValue($user->getPosition())
            ->setCode(UplinePosition::POSITION_ALREADY_TAKEN)
            ->addViolation();
    }

    /**
     * @param string $currentPosition
     */
    private function getOppositeOfTheCurrentPosition(string $currentPosition): string
    {
        if ($currentPosition == 'left') {
            return 'right';
        } else {
            return 'left';
        }
    }
}
