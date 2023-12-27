<?php

namespace App\Library\Constraint;

use App\Service\Validator\NotBlankContactEmailValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered contact e-mail is not empty.
 */
#[Attribute]
class NotBlankContactEmail extends Constraint
{
    public string $messageWhenMandatory = 'email_mandatory';
    public string $messageWhenNotMandatory = 'email_or_phone_number_mandatory';
    public string $emailProperty = 'email';
    public string $phoneNumberProperty = 'phoneNumber';
    public string $isEmailMandatoryProperty = 'isEmailMandatory';
    public string $isPhoneNumberMandatoryProperty = 'isPhoneNumberMandatory';

    public function __construct(string $messageWhenMandatory = null,
                                string $messageWhenNotMandatory = null,
                                string $emailProperty = null,
                                string $phoneNumberProperty = null,
                                string $isEmailMandatoryProperty = null,
                                string $isPhoneNumberMandatoryProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->messageWhenMandatory = $messageWhenMandatory ?? $this->messageWhenMandatory;
        $this->messageWhenNotMandatory = $messageWhenNotMandatory ?? $this->messageWhenNotMandatory;
        $this->emailProperty = $emailProperty ?? $this->emailProperty;
        $this->phoneNumberProperty = $phoneNumberProperty ?? $this->phoneNumberProperty;
        $this->isEmailMandatoryProperty = $isEmailMandatoryProperty ?? $this->isEmailMandatoryProperty;
        $this->isPhoneNumberMandatoryProperty = $isPhoneNumberMandatoryProperty ?? $this->isPhoneNumberMandatoryProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return NotBlankContactEmailValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}