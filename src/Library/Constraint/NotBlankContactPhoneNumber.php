<?php

namespace App\Library\Constraint;

use App\Service\Validator\NotBlankContactPhoneNumberValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered contact phone number is not empty.
 */
#[Attribute]
class NotBlankContactPhoneNumber extends Constraint
{
    public string $messageWhenMandatory = 'phone_number_mandatory';
    public string $messageWhenNotMandatory = 'email_or_phone_number_mandatory';
    public string $phoneNumberProperty = 'phoneNumber';
    public string $emailProperty = 'email';

    public function __construct(string $messageWhenMandatory = null,
                                string $messageWhenNotMandatory = null,
                                string $phoneNumberProperty = null,
                                string $emailProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->messageWhenMandatory = $messageWhenMandatory ?? $this->messageWhenMandatory;
        $this->messageWhenNotMandatory = $messageWhenNotMandatory ?? $this->messageWhenNotMandatory;
        $this->phoneNumberProperty = $phoneNumberProperty ?? $this->phoneNumberProperty;
        $this->emailProperty = $emailProperty ?? $this->emailProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return NotBlankContactPhoneNumberValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}