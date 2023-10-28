<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueCampCategoryValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered url name is not yet assigned to the parent.
 */
#[Attribute]
class UniqueCampCategory extends Constraint
{
    public string $message = 'unique_camp_category';
    public string $urlNameProperty = 'urlName';
    public string $parentProperty = 'parent';
    public string $campCategoryProperty = 'campCategory';

    public function __construct(string $message = null,
                                string $urlNameProperty = null,
                                string $parentProperty = null,
                                string $campCategoryProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->urlNameProperty = $urlNameProperty ?? $this->urlNameProperty;
        $this->parentProperty = $parentProperty ?? $this->parentProperty;
        $this->campCategoryProperty = $campCategoryProperty ?? $this->campCategoryProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueCampCategoryValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}