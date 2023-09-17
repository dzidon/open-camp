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
    public string $idProperty = 'id';

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