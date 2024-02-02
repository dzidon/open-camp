<?php

namespace App\Service\Validator;

use App\Library\Constraint\DiscountSiblingIntervalInConfig;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DiscountSiblingIntervalInConfigValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof DiscountSiblingIntervalInConfig)
        {
            throw new UnexpectedTypeException($constraint, DiscountSiblingIntervalInConfig::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $data = $value;
        $discountSiblingsConfig = $this->propertyAccessor->getValue($data, $constraint->discountSiblingsConfigProperty);

        if (!is_array($discountSiblingsConfig))
        {
            throw new UnexpectedTypeException($discountSiblingsConfig, 'array');
        }

        $discountSiblingsInterval = $this->propertyAccessor->getValue($data, $constraint->discountSiblingsIntervalProperty);

        if ($discountSiblingsInterval !== false && !is_array($discountSiblingsInterval))
        {
            throw new UnexpectedTypeException($discountSiblingsInterval, 'array|null');
        }

        if ($discountSiblingsInterval === false)
        {
            return;
        }

        $foundInConfig = false;
        $discountSiblingsIntervalFrom = $discountSiblingsInterval[array_key_first($discountSiblingsInterval)];
        $discountSiblingsIntervalTo = $discountSiblingsInterval[array_key_last($discountSiblingsInterval)];

        foreach ($discountSiblingsConfig as $options)
        {
            $configFrom = $options['from'];
            $configTo = $options['to'];

            if ($discountSiblingsIntervalFrom === $configFrom && $discountSiblingsIntervalTo === $configTo)
            {
                $foundInConfig = true;

                break;
            }
        }

        if (!$foundInConfig)
        {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->discountSiblingsIntervalProperty)
                ->addViolation()
            ;
        }
    }
}