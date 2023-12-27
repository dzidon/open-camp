<?php

namespace App\Service\Validator;

use App\Library\Constraint\ApplicationAttachmentFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraints as Assert;

class ApplicationAttachmentFileValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ApplicationAttachmentFile)
        {
            throw new UnexpectedTypeException($constraint, ApplicationAttachmentFile::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $applicationAttachmentData = $value;
        $maxSize = $this->propertyAccessor->getValue($applicationAttachmentData, $constraint->maxSizeProperty);

        if (!is_float($maxSize))
        {
            throw new UnexpectedTypeException($maxSize, 'float');
        }

        $extensions = $this->propertyAccessor->getValue($applicationAttachmentData, $constraint->extensionsProperty);

        if (!is_array($extensions))
        {
            throw new UnexpectedTypeException($extensions, 'array');
        }

        $file = $this->propertyAccessor->getValue($applicationAttachmentData, $constraint->fileProperty);

        if ($file !== null && !$file instanceof File)
        {
            throw new UnexpectedTypeException($file, File::class);
        }

        if ($file === null)
        {
            return;
        }

        $validator = $this->context->getValidator();
        $violations = $validator->validate($file, [
            new Assert\File(
                maxSize: sprintf('%sM', $maxSize),
                maxSizeMessage: $constraint->messageSize,
                extensions: $extensions,
                extensionsMessage: $constraint->messageExtension,
            ),
        ]);

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation)
        {
            $message = $violation->getMessage();

            $this->context
                ->buildViolation($message)
                ->atPath($constraint->fileProperty)
                ->addViolation()
            ;
        }
    }
}