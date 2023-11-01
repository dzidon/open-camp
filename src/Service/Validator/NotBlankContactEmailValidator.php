<?php

namespace App\Service\Validator;

use App\Library\Constraint\NotBlankContactEmail;
use libphonenumber\PhoneNumber;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered contact e-mail is not empty.
 */
class NotBlankContactEmailValidator extends ConstraintValidator
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
        if (!$constraint instanceof NotBlankContactEmail)
        {
            throw new UnexpectedTypeException($constraint, NotBlankContactEmail::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $contactData = $value;
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

        if ($this->isEmailMandatory)
        {
            $message = $constraint->messageWhenMandatory;
        }
        else if (!$this->isPhoneNumberMandatory && $phoneNumber === null)
        {
            $message = $constraint->messageWhenNotMandatory;
        }

        if ($message !== null)
        {
            $message = $this->translator->trans($message, [], 'validators');

            $this->context->buildViolation($message)
                ->atPath($constraint->emailProperty)
                ->addViolation()
            ;
        }
    }
}