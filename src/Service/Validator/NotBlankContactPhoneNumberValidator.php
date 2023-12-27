<?php

namespace App\Service\Validator;

use App\Library\Constraint\NotBlankContactPhoneNumber;
use libphonenumber\PhoneNumber;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered contact phone number is not empty.
 */
class NotBlankContactPhoneNumberValidator extends ConstraintValidator
{
    private TranslatorInterface $translator;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(TranslatorInterface $translator, PropertyAccessorInterface $propertyAccessor)
    {
        $this->translator = $translator;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotBlankContactPhoneNumber)
        {
            throw new UnexpectedTypeException($constraint, NotBlankContactPhoneNumber::class);
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

        $phoneNumber = $this->propertyAccessor->getValue($contactData, $constraint->phoneNumberProperty);

        if ($phoneNumber !== null)
        {
            if ($phoneNumber instanceof PhoneNumber)
            {
                return;
            }

            throw new UnexpectedTypeException($phoneNumber, PhoneNumber::class);
        }

        $email = $this->propertyAccessor->getValue($contactData, $constraint->emailProperty);

        if ($email !== null && !is_string($email))
        {
            throw new UnexpectedTypeException($email, 'null|string');
        }

        $message = null;

        if ($isPhoneNumberMandatory)
        {
            $message = $constraint->messageWhenMandatory;
        }
        else if (!$isEmailMandatory && ($email === null || $email === ''))
        {
            $message = $constraint->messageWhenNotMandatory;
        }

        if ($message !== null)
        {
            $message = $this->translator->trans($message, [], 'validators');

            $this->context->buildViolation($message)
                ->atPath($constraint->phoneNumberProperty)
                ->addViolation()
            ;
        }
    }
}