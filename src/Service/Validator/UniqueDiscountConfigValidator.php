<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueDiscountConfig;
use App\Model\Entity\DiscountConfig;
use App\Model\Repository\DiscountConfigRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered name is not yet assigned to any discount config.
 */
class UniqueDiscountConfigValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private DiscountConfigRepositoryInterface $discountConfigRepository;

    public function __construct(PropertyAccessorInterface         $propertyAccessor,
                                DiscountConfigRepositoryInterface $discountConfigRepository)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->discountConfigRepository = $discountConfigRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueDiscountConfig)
        {
            throw new UnexpectedTypeException($constraint, UniqueDiscountConfig::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $discountConfigData = $value;
        $name = $this->propertyAccessor->getValue($discountConfigData, $constraint->nameProperty);

        if ($name !== null && !is_string($name))
        {
            throw new UnexpectedTypeException($name, 'string');
        }

        $discountConfig = $this->propertyAccessor->getValue($discountConfigData, $constraint->discountConfigProperty);

        if ($discountConfig !== null && !$discountConfig instanceof DiscountConfig)
        {
            throw new UnexpectedTypeException($discountConfig, DiscountConfig::class);
        }

        if ($name === null || $name === '')
        {
            return;
        }

        $existingDiscountConfig = $this->discountConfigRepository->findOneByName($name);

        if ($existingDiscountConfig === null)
        {
            return;
        }

        $id = $discountConfig?->getId();
        $existingId = $existingDiscountConfig->getId();

        if ($id === null || $id->toRfc4122() !== $existingId->toRfc4122())
        {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->nameProperty)
                ->addViolation()
            ;
        }
    }
}