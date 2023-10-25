<?php

namespace App\Model\Module\Application\FormField;

use App\Model\Enum\Entity\FormFieldTypeEnum;

/**
 * OptionsResolver wrapper for custom form fields.
 */
interface FormFieldOptionsResolverInterface
{
    /**
     * Resolves options for a custom form field type.
     *
     * @param FormFieldTypeEnum $type
     * @param array $options
     * @return array
     */
    public function resolve(FormFieldTypeEnum $type, array $options): array;
}