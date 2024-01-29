<?php

namespace App\Library\Constraint;

use App\Service\Validator\CampDateUserUniqueValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered user is assigned to the given camp date only once.
 */
#[Attribute]
class CampDateUserUnique extends Constraint
{
    public string $message = 'camp_date_user_unique';
    public string $userProperty = 'user';

    public function __construct(string $message = null,
                                string $userProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->userProperty = $userProperty ?? $this->userProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return CampDateUserUniqueValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}