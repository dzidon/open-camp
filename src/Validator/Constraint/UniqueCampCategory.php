<?php

namespace App\Validator\Constraint;

use App\Validator\Validator\UniqueCampCategoryValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered url name is not yet assigned to the parent.
 */
#[Attribute]
class UniqueCampCategory extends Constraint
{
    public string $message = 'constraint.unique_camp_category';
    public string $urlNameProperty = 'urlName';
    public string $parentProperty = 'parent';
    public string $idProperty = 'id';

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