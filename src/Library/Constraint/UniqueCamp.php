<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueCampValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered url name is not yet assigned to any camp.
 */
#[Attribute]
class UniqueCamp extends Constraint
{
    public string $message = 'unique_camp';
    public string $urlNameProperty = 'urlName';
    public string $idProperty = 'id';

    public function __construct(string $message = null,
                                string $urlNameProperty = null,
                                string $idProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->urlNameProperty = $urlNameProperty ?? $this->urlNameProperty;
        $this->idProperty = $idProperty ?? $this->idProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueCampValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}