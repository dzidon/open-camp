<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueCamp;
use App\Model\Repository\CampRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered url name is not yet assigned to any camp.
 */
class UniqueCampValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private CampRepositoryInterface $campRepository;
    private TranslatorInterface $translator;

    public function __construct(PropertyAccessorInterface $propertyAccessor,
                                CampRepositoryInterface   $campRepository,
                                TranslatorInterface       $translator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->campRepository = $campRepository;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueCamp)
        {
            throw new UnexpectedTypeException($constraint, UniqueCamp::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $campData = $value;
        $urlName = $this->propertyAccessor->getValue($campData, $constraint->urlNameProperty);

        if ($urlName !== null && !is_string($urlName))
        {
            throw new UnexpectedTypeException($urlName, 'string');
        }

        $id = $this->propertyAccessor->getValue($campData, $constraint->idProperty);

        if ($id !== null && !$id instanceof UuidV4)
        {
            throw new UnexpectedTypeException($id, UuidV4::class);
        }

        if ($urlName === null || $urlName === '')
        {
            return;
        }

        $existingCamp = $this->campRepository->findOneByUrlName($urlName);

        if ($existingCamp === null)
        {
            return;
        }

        $existingId = $existingCamp->getId();

        if ($id === null || $id->toRfc4122() !== $existingId->toRfc4122())
        {
            $message = $this->translator->trans($constraint->message, [], 'validators');

            $this->context
                ->buildViolation($message)
                ->atPath($constraint->urlNameProperty)
                ->addViolation()
            ;
        }
    }
}