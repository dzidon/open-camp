<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueAttachmentConfig;
use App\Model\Entity\AttachmentConfig;
use App\Model\Repository\AttachmentConfigRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered name is not yet assigned to any attachment config.
 */
class UniqueAttachmentConfigValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private AttachmentConfigRepositoryInterface $attachmentConfigRepository;

    public function __construct(PropertyAccessorInterface           $propertyAccessor,
                                AttachmentConfigRepositoryInterface $attachmentConfigRepository)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->attachmentConfigRepository = $attachmentConfigRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueAttachmentConfig)
        {
            throw new UnexpectedTypeException($constraint, UniqueAttachmentConfig::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $attachmentConfigData = $value;
        $name = $this->propertyAccessor->getValue($attachmentConfigData, $constraint->nameProperty);

        if ($name !== null && !is_string($name))
        {
            throw new UnexpectedTypeException($name, 'string');
        }

        $attachmentConfig = $this->propertyAccessor->getValue($attachmentConfigData, $constraint->attachmentConfigProperty);

        if ($attachmentConfig !== null && !$attachmentConfig instanceof AttachmentConfig)
        {
            throw new UnexpectedTypeException($attachmentConfig, AttachmentConfig::class);
        }

        if ($name === null || $name === '')
        {
            return;
        }

        $existingAttachmentConfig = $this->attachmentConfigRepository->findOneByName($name);

        if ($existingAttachmentConfig === null)
        {
            return;
        }

        $id = $attachmentConfig?->getId();
        $existingId = $existingAttachmentConfig->getId();

        if ($id === null || $id->toRfc4122() !== $existingId->toRfc4122())
        {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->nameProperty)
                ->addViolation()
            ;
        }
    }
}