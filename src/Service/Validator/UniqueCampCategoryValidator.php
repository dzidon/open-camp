<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueCampCategory;
use App\Model\Entity\CampCategory;
use App\Model\Repository\CampCategoryRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
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
            throw new UnexpectedTypeException($constraint, UniqueCampCategory::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $campCategoryData = $value;
        $parent = $this->propertyAccessor->getValue($campCategoryData, $constraint->parentProperty);

        if ($parent !== null && !$parent instanceof CampCategory)
        {
            throw new UnexpectedTypeException($parent, CampCategory::class);
        }

        $urlName = $this->propertyAccessor->getValue($campCategoryData, $constraint->urlNameProperty);

        if ($urlName !== null && !is_string($urlName))
        {
            throw new UnexpectedTypeException($urlName, 'string');
        }

        $id = $this->propertyAccessor->getValue($campCategoryData, $constraint->idProperty);

        if ($id !== null && !$id instanceof UuidV4)
        {
            throw new UnexpectedTypeException($id, UuidV4::class);
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
                $message = $this->translator->trans($constraint->message, [], 'validators');

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