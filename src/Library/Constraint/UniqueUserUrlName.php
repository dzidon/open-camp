<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueUserUrlNameValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered url name is not yet assigned to a user.
 */
#[Attribute]
class UniqueUserUrlName extends Constraint
{
    public string $message = 'unique_user_url_name';
    public string $userProperty = 'user';
    public string $urlNameProperty = 'urlName';

    public function __construct(string $message = null,
                                string $userProperty = null,
                                string $urlNameProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->userProperty = $userProperty ?? $this->userProperty;
        $this->urlNameProperty = $urlNameProperty ?? $this->urlNameProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueUserUrlNameValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}