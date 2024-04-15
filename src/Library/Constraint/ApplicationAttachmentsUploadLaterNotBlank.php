<?php

namespace App\Library\Constraint;

use App\Service\Validator\ApplicationAttachmentsUploadLaterNotBlankValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that at least one file was uploaded later.
 */
#[Attribute]
class ApplicationAttachmentsUploadLaterNotBlank extends Constraint
{
    public string $message = 'application_attachments_upload_later_mandatory';
    public string $outerApplicationAttachmentsDataProperty = 'applicationAttachmentsData';
    public string $innerApplicationAttachmentsDataProperty = 'applicationAttachmentsData';
    public string $fileProperty = 'file';

    public function __construct(string $message = null,
                                string $outerApplicationAttachmentsDataProperty = null,
                                string $innerApplicationAttachmentsDataProperty = null,
                                string $fileProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->outerApplicationAttachmentsDataProperty = $outerApplicationAttachmentsDataProperty ?? $this->outerApplicationAttachmentsDataProperty;
        $this->innerApplicationAttachmentsDataProperty = $innerApplicationAttachmentsDataProperty ?? $this->innerApplicationAttachmentsDataProperty;
        $this->fileProperty = $fileProperty ?? $this->fileProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return ApplicationAttachmentsUploadLaterNotBlankValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}