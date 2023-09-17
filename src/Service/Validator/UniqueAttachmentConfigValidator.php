<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueAttachmentConfig;
use App\Model\Repository\AttachmentConfigRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered name is not yet assigned to any attachment config.
 */
class UniqueAttachmentConfigValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private AttachmentConfigRepositoryInterface $attachmentConfigRepository;
    private TranslatorInterface $translator;

    public function __construct(PropertyAccessorInterface           $propertyAccessor,
                                AttachmentConfigRepositoryInterface $attachmentConfigRepository,
                                TranslatorInterface                 $translator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->attachmentConfigRepository = $attachmentConfigRepository;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueAttachmentConfig)
        {
            throw new UnexpectedValueException($constraint, UniqueAttachmentConfig::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedValueException($value, 'object');
        }

        $attachmentConfigData = $value;
        $name = $this->propertyAccessor->getValue($attachmentConfigData, $constraint->nameProperty);

        if ($name !== null && !is_string($name))
        {
            throw new UnexpectedValueException($name, 'string');
        }

        $id = $this->propertyAccessor->getValue($attachmentConfigData, $constraint->idProperty);

        if ($id !== null && !$id instanceof UuidV4)
        {
            throw new UnexpectedValueException($id, UuidV4::class);
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

        $existingId = $existingAttachmentConfig->getId();

        if ($id === null || $id->toRfc4122() !== $existingId->toRfc4122())
        {
            $message = $this->translator->trans($constraint->message, [], 'validators');

            $this->context
                ->buildViolation($message)
                ->atPath($constraint->nameProperty)
                ->addViolation()
            ;
        }
    }
}