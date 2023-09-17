<?php

namespace App\Model\Enum\Entity;

/**
 * Required types of the attachment config entity.
 */
enum AttachmentConfigRequiredTypeEnum: string
{
    case OPTIONAL = 'optional';
    case REQUIRED = 'required';
    case REQUIRED_LATER = 'required_later';
}
