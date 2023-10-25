<?php

namespace App\Model\Enum\Entity;

/**
 * Types of form fields.
 */
enum FormFieldTypeEnum: string
{
    case TEXT = 'text';
    case TEXT_AREA = 'text_area';
    case NUMBER = 'number';
    case CHOICE = 'choice';
}
