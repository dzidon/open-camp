<?php

namespace App\Service\Validator;

use App\Library\Constraint\ApplicationFormFieldValue;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationFormFieldValueValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ApplicationFormFieldValue)
        {
            throw new UnexpectedTypeException($constraint, ApplicationFormFieldValue::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $applicationFormFieldValueData = $value;
        $type = $this->propertyAccessor->getValue($applicationFormFieldValueData, $constraint->typeProperty);

        if (!in_array($type, FormFieldTypeEnum::cases()))
        {
            throw new UnexpectedTypeException($type, FormFieldTypeEnum::class);
        }

        $options = $this->propertyAccessor->getValue($applicationFormFieldValueData, $constraint->optionsProperty);

        if (!is_array($options))
        {
            throw new UnexpectedTypeException($options, 'array');
        }

        $value = $this->propertyAccessor->getValue($applicationFormFieldValueData, $constraint->valueProperty);

        if ($value !== null && !is_string($value) && !is_array($value))
        {
            throw new UnexpectedTypeException($value, 'string|array');
        }

        if ($value === null || $value === '' || $value === [])
        {
            return;
        }

        $validator = $this->context->getValidator();
        $violations = [];

        if ($type === FormFieldTypeEnum::TEXT || $type === FormFieldTypeEnum::TEXT_AREA)
        {
            $violations = $this->validateTextValue($value, $options, $validator);
        }
        else if ($type === FormFieldTypeEnum::NUMBER)
        {
            $violations = $this->validateNumberValue($value, $options, $validator);
        }
        else if ($type === FormFieldTypeEnum::CHOICE)
        {
            $violations = $this->validateChoiceValue($value, $options, $validator);
        }

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation)
        {
            $message = $violation->getMessage();

            $this->context
                ->buildViolation($message)
                ->atPath($constraint->valueProperty)
                ->addViolation()
            ;
        }
    }

    /**
     * @param string $value
     * @param array $options
     * @param ValidatorInterface $validator
     * @return ConstraintViolationListInterface
     */
    private function validateTextValue(string $value, array $options, ValidatorInterface $validator): ConstraintViolationListInterface
    {
        $lengthMin = array_key_exists('length_min', $options) ? $options['length_min'] : null;
        $lengthMax = array_key_exists('length_max', $options) ? $options['length_max'] : null;
        $regex = array_key_exists('regex', $options) ? $options['regex'] : null;

        $constraints = [];

        if ($lengthMin !== null || $lengthMax !== null)
        {
            $constraints[] = new Assert\Length(min: $lengthMin, max: $lengthMax);
        }

        if ($regex !== null)
        {
            $pattern = sprintf('/%s/', $regex);
            $constraints[] = new Assert\Regex(pattern: $pattern);
        }

        return $validator->validate($value, $constraints);
    }

    /**
     * @param string $value
     * @param array $options
     * @param ValidatorInterface $validator
     * @return ConstraintViolationListInterface
     */
    private function validateNumberValue(string $value, array $options, ValidatorInterface $validator): ConstraintViolationListInterface
    {
        $min = array_key_exists('min', $options) ? $options['min'] : null;
        $max = array_key_exists('max', $options) ? $options['max'] : null;

        $constraints = [];

        if ($min !== null)
        {
            $constraints[] = new Assert\GreaterThanOrEqual(value: $min);
        }

        if ($max !== null)
        {
            $constraints[] = new Assert\LessThanOrEqual(value: $max);
        }

        return $validator->validate($value, $constraints);
    }

    /**
     * @param array $value
     * @param array $options
     * @param ValidatorInterface $validator
     * @return ConstraintViolationListInterface
     */
    private function validateChoiceValue(array $value, array $options, ValidatorInterface $validator): ConstraintViolationListInterface
    {
        $multiple = array_key_exists('multiple', $options) ? $options['multiple'] : false;
        $items = array_key_exists('items', $options) ? $options['items'] : [];

        $constraints = [new Assert\Choice(choices: $items, multiple: $multiple)];

        if (!$multiple)
        {
            $constraints[] = new Assert\Count(max: 1);
        }

        return $validator->validate($value, $constraints);
    }
}