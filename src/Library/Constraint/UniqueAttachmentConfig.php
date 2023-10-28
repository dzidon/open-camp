<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueAttachmentConfigValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered name is not yet assigned to any attachment config.
 */
#[Attribute]
class UniqueAttachmentConfig extends Constraint
{
    public string $message = 'unique_attachment_config';
    public string $nameProperty = 'name';
    public string $attachmentConfigProperty = 'attachmentConfig';

    public function __construct(string $message = null,
                                string $nameProperty = null,
                                string $attachmentConfigProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->nameProperty = $nameProperty ?? $this->nameProperty;
        $this->attachmentConfigProperty = $attachmentConfigProperty ?? $this->attachmentConfigProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueAttachmentConfigValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}