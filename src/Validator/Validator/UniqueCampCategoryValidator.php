<?php

namespace App\Validator\Validator;

use App\Model\Entity\CampCategory;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Validator\Constraint\UniqueCampCategory;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered url name is not yet assigned to the parent.
 */
class UniqueCampCategoryValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private CampCategoryRepositoryInterface $campCategoryRepository;
    private TranslatorInterface $translator;

    public function __construct(PropertyAccessorInterface       $propertyAccessor,
                                CampCategoryRepositoryInterface $campCategoryRepository,
                                TranslatorInterface             $translator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->campCategoryRepository = $campCategoryRepository;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueCampCategory)
        {
            throw new UnexpectedValueException($constraint, UniqueCampCategory::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedValueException($value, 'object');
        }

        $campCategoryData = $value;
        $parent = $this->propertyAccessor->getValue($campCategoryData, $constraint->parentProperty);

        if ($parent !== null && !$parent instanceof CampCategory)
        {
            throw new UnexpectedValueException($parent, CampCategory::class);
        }

        $urlName = $this->propertyAccessor->getValue($campCategoryData, $constraint->urlNameProperty);

        if ($urlName !== null && !is_string($urlName))
        {
            throw new UnexpectedValueException($urlName, 'string');
        }

        $id = $this->propertyAccessor->getValue($campCategoryData, $constraint->idProperty);

        if ($id !== null && !$id instanceof UuidV4)
        {
            throw new UnexpectedValueException($id, UuidV4::class);
        }

        if ($urlName === null || $urlName === '')
        {
            return;
        }

        $existingCampCategories = $this->campCategoryRepository->findByUrlName($urlName);

        if (empty($existingCampCategories))
        {
            return;
        }

        foreach ($existingCampCategories as $existingCampCategory)
        {
            $existingId = $existingCampCategory->getId();
            $existingParent = $existingCampCategory->getParent();

            if ($parent === $existingParent && ($id === null || $id->toRfc4122() !== $existingId->toRfc4122()))
            {
                $message = $this->translator->trans($constraint->message);

                $this->context
                    ->buildViolation($message)
                    ->atPath($constraint->urlNameProperty)
                    ->addViolation()
                ;

                break;
            }
        }
    }
}