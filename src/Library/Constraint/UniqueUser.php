<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueUserValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered e-mail is not yet registered.
 */
#[Attribute]
class UniqueUser extends Constraint
{
    public string $message = 'unique_user';
    public string $userProperty = 'user';
    public string $emailProperty = 'email';

    public function __construct(string $message = null,
                                string $userProperty = null,
                                string $emailProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->userProperty = $userProperty ?? $this->userProperty;
        $this->emailProperty = $emailProperty ?? $this->emailProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueUserValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}