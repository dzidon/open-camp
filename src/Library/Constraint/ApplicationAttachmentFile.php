<?php

namespace App\Library\Constraint;

use App\Service\Validator\ApplicationAttachmentFileValidator;
use Attribute;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * Validates the uploaded application attachment.
 */
#[Attribute]
class ApplicationAttachmentFile extends Constraint
{
    public string $messageExtension = 'invalid_file_extension';
    public string $messageSize = 'file_too_large';
    public string $maxSizeProperty = 'maxSize';
    public string $extensionsProperty = 'extensions';
    public string $fileProperty = 'file';

    #[HasNamedArguments]
    public function __construct(string $messageExtension = null,
                                string $messageSize = null,
                                string $maxSizeProperty = null,
                                string $extensionsProperty = null,
                                string $fileProperty = null,
                                array $groups = null,
                                mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->messageExtension = $messageExtension ?? $this->messageExtension;
        $this->messageSize = $messageSize ?? $this->messageSize;
        $this->maxSizeProperty = $maxSizeProperty ?? $this->maxSizeProperty;
        $this->extensionsProperty = $extensionsProperty ?? $this->extensionsProperty;
        $this->fileProperty = $fileProperty ?? $this->fileProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return ApplicationAttachmentFileValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}