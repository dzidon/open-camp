<?php

namespace App\Service\Validator;

use App\Library\Constraint\ApplicationAttachmentsUploadLaterNotBlank;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that at least one file was uploaded later.
 */
class ApplicationAttachmentsUploadLaterNotBlankValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ApplicationAttachmentsUploadLaterNotBlank)
        {
            throw new UnexpectedTypeException($constraint, ApplicationAttachmentsUploadLaterNotBlank::class);
        }

        $applicationAttachmentsUploadLaterData = $value;

        if (!is_object($applicationAttachmentsUploadLaterData))
        {
            throw new UnexpectedTypeException($applicationAttachmentsUploadLaterData, 'object');
        }

        $outerApplicationAttachmentsData = $this->propertyAccessor->getValue(
            $applicationAttachmentsUploadLaterData,
            $constraint->outerApplicationAttachmentsDataProperty
        );

        if (!is_array($outerApplicationAttachmentsData))
        {
            throw new UnexpectedTypeException($outerApplicationAttachmentsData, 'array');
        }

        $filesAreEmpty = true;

        foreach ($outerApplicationAttachmentsData as $outerApplicationAttachmentsDatum)
        {
            $innerApplicationAttachmentsData = $this->propertyAccessor->getValue(
                $outerApplicationAttachmentsDatum,
                $constraint->innerApplicationAttachmentsDataProperty
            );

            if (!is_array($innerApplicationAttachmentsData))
            {
                throw new UnexpectedTypeException($innerApplicationAttachmentsData, 'array');
            }

            foreach ($innerApplicationAttachmentsData as $innerApplicationAttachmentsDatum)
            {
                $file = $this->propertyAccessor->getValue($innerApplicationAttachmentsDatum, $constraint->fileProperty);

                if ($file !== null)
                {
                    if (!$file instanceof File)
                    {
                        throw new UnexpectedTypeException($file, File::class);
                    }

                    $filesAreEmpty = false;
                }
            }
        }

        if ($filesAreEmpty)
        {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}