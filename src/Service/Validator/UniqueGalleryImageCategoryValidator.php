<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueGalleryImageCategory;
use App\Model\Entity\GalleryImageCategory;
use App\Model\Repository\GalleryImageCategoryRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered url name is not yet assigned to the parent.
 */
class UniqueGalleryImageCategoryValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private GalleryImageCategoryRepositoryInterface $galleryImageCategoryRepository;

    public function __construct(PropertyAccessorInterface               $propertyAccessor,
                                GalleryImageCategoryRepositoryInterface $galleryImageCategoryRepository)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->galleryImageCategoryRepository = $galleryImageCategoryRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueGalleryImageCategory)
        {
            throw new UnexpectedTypeException($constraint, UniqueGalleryImageCategory::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $galleryImageCategoryData = $value;
        $parent = $this->propertyAccessor->getValue($galleryImageCategoryData, $constraint->parentProperty);

        if ($parent !== null && !$parent instanceof GalleryImageCategory)
        {
            throw new UnexpectedTypeException($parent, GalleryImageCategory::class);
        }

        $urlName = $this->propertyAccessor->getValue($galleryImageCategoryData, $constraint->urlNameProperty);

        if ($urlName !== null && !is_string($urlName))
        {
            throw new UnexpectedTypeException($urlName, 'string');
        }

        $galleryImageCategory = $this->propertyAccessor->getValue($galleryImageCategoryData, $constraint->galleryImageCategoryProperty);

        if ($galleryImageCategory !== null && !$galleryImageCategory instanceof GalleryImageCategory)
        {
            throw new UnexpectedTypeException($galleryImageCategory, GalleryImageCategory::class);
        }

        if ($urlName === null || $urlName === '')
        {
            return;
        }

        $existingCampCategories = $this->galleryImageCategoryRepository->findByUrlName($urlName);

        if (empty($existingCampCategories))
        {
            return;
        }

        $id = $galleryImageCategory?->getId();

        foreach ($existingCampCategories as $existingGalleryImageCategory)
        {
            $existingId = $existingGalleryImageCategory->getId();
            $existingParent = $existingGalleryImageCategory->getParent();

            if ($parent === $existingParent && ($id === null || $id->toRfc4122() !== $existingId->toRfc4122()))
            {
                $this->context
                    ->buildViolation($constraint->message)
                    ->atPath($constraint->urlNameProperty)
                    ->addViolation()
                ;

                break;
            }
        }
    }
}