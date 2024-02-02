<?php

namespace App\Service\Validator;

use App\Library\Constraint\NotBlankContactEmail;
use libphonenumber\PhoneNumber;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered contact e-mail is not empty.
 */
class NotBlankContactEmailValidator extends ConstraintValidator
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
        if (!$constraint instanceof NotBlankContactEmail)
        {
            throw new UnexpectedTypeException($constraint, NotBlankContactEmail::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $contactData = $value;
        $isEmailMandatory = $this->propertyAccessor->getValue($contactData, $constraint->isEmailMandatoryProperty);

        if (!is_bool($isEmailMandatory))
        {
            throw new UnexpectedTypeException($isEmailMandatory, 'bool');
        }

        $isPhoneNumberMandatory = $this->propertyAccessor->getValue($contactData, $constraint->isPhoneNumberMandatoryProperty);

        if (!is_bool($isPhoneNumberMandatory))
        {
            throw new UnexpectedTypeException($isPhoneNumberMandatory, 'bool');
        }

        $email = $this->propertyAccessor->getValue($contactData, $constraint->emailProperty);

        if ($email !== null)
        {
            if (!is_string($email))
            {
                throw new UnexpectedTypeException($email, 'null|string');
            }

            if ($email !== '')
            {
                return;
            }
        }

        $phoneNumber = $this->propertyAccessor->getValue($contactData, $constraint->phoneNumberProperty);

        if ($phoneNumber !== null && !$phoneNumber instanceof PhoneNumber)
        {
            throw new UnexpectedTypeException($phoneNumber, PhoneNumber::class);
        }

        $message = null;

        if ($isEmailMandatory)
        {
            $message = $constraint->messageWhenMandatory;
        }
        else if (!$isPhoneNumberMandatory && $phoneNumber === null)
        {
            $message = $constraint->messageWhenNotMandatory;
        }

        if ($message !== null)
        {
            $this->context->buildViolation($message)
                ->atPath($constraint->emailProperty)
                ->addViolation()
            ;
        }
    }
}