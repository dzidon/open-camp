<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniquePageValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered url name is not yet assigned to any page.
 */
#[Attribute]
class UniquePage extends Constraint
{
    public string $message = 'unique_page';
    public string $urlNameProperty = 'urlName';
    public string $pageProperty = 'page';

    public function __construct(string $message = null,
                                string $urlNameProperty = null,
                                string $pageProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->urlNameProperty = $urlNameProperty ?? $this->urlNameProperty;
        $this->pageProperty = $pageProperty ?? $this->pageProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniquePageValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}