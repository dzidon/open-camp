<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueCamp;
use App\Model\Entity\Camp;
use App\Model\Repository\CampRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered url name is not yet assigned to any camp.
 */
class UniqueCampValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private CampRepositoryInterface $campRepository;

    public function __construct(PropertyAccessorInterface $propertyAccessor,
                                CampRepositoryInterface   $campRepository)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->campRepository = $campRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueCamp)
        {
            throw new UnexpectedTypeException($constraint, UniqueCamp::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $campData = $value;
        $urlName = $this->propertyAccessor->getValue($campData, $constraint->urlNameProperty);

        if ($urlName !== null && !is_string($urlName))
        {
            throw new UnexpectedTypeException($urlName, 'string');
        }

        $camp = $this->propertyAccessor->getValue($campData, $constraint->campProperty);

        if ($camp !== null && !$camp instanceof Camp)
        {
            throw new UnexpectedTypeException($camp, Camp::class);
        }

        if ($urlName === null || $urlName === '')
        {
            return;
        }

        $existingCamp = $this->campRepository->findOneByUrlName($urlName);

        if ($existingCamp === null)
        {
            return;
        }

        $id = $camp?->getId();
        $existingId = $existingCamp->getId();

        if ($id === null || $id->toRfc4122() !== $existingId->toRfc4122())
        {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->urlNameProperty)
                ->addViolation()
            ;
        }
    }
}