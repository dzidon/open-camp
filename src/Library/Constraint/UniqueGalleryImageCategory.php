<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueGalleryImageCategoryValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered url name is not yet assigned to the parent.
 */
#[Attribute]
class UniqueGalleryImageCategory extends Constraint
{
    public string $message = 'unique_camp_category';
    public string $urlNameProperty = 'urlName';
    public string $parentProperty = 'parent';
    public string $galleryImageCategoryProperty = 'galleryImageCategory';

    public function __construct(string $message = null,
                                string $urlNameProperty = null,
                                string $parentProperty = null,
                                string $galleryImageCategoryProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->urlNameProperty = $urlNameProperty ?? $this->urlNameProperty;
        $this->parentProperty = $parentProperty ?? $this->parentProperty;
        $this->galleryImageCategoryProperty = $galleryImageCategoryProperty ?? $this->galleryImageCategoryProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueGalleryImageCategoryValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}