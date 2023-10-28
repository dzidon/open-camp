<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueRoleValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered label is not yet assigned to any role.
 */
#[Attribute]
class UniqueRole extends Constraint
{
    public string $message = 'unique_role';
    public string $labelProperty = 'label';
    public string $roleProperty = 'role';

    public function __construct(string $message = null,
                                string $labelProperty = null,
                                string $roleProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->labelProperty = $labelProperty ?? $this->labelProperty;
        $this->roleProperty = $roleProperty ?? $this->roleProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueRoleValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}