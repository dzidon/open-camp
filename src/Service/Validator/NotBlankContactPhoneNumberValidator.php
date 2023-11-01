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
    private bool $isEmailMandatory;
    private bool $isPhoneNumberMandatory;

    private TranslatorInterface $translator;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(TranslatorInterface       $translator,
                                PropertyAccessorInterface $propertyAccessor,
                                bool                      $isEmailMandatory,
                                bool                      $isPhoneNumberMandatory)
    {
        $this->translator = $translator;
        $this->propertyAccessor = $propertyAccessor;
        $this->isEmailMandatory = $isEmailMandatory;
        $this->isPhoneNumberMandatory = $isPhoneNumberMandatory;
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

        if ($this->isPhoneNumberMandatory)
        {
            $message = $constraint->messageWhenMandatory;
        }
        else if (!$this->isEmailMandatory && ($email === null || $email === ''))
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