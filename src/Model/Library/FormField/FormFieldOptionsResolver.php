<?php

namespace App\Model\Library\FormField;

use App\Model\Enum\Entity\FormFieldTypeEnum;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @inheritDoc
 */
class FormFieldOptionsResolver implements FormFieldOptionsResolverInterface
{
    private OptionsResolver $resolver;

    public function __construct()
    {
        $this->resolver = new OptionsResolver();
    }

    /**
     * @inheritDoc
     */
    public function resolve(FormFieldTypeEnum $type, array $options): array
    {
        if ($type === FormFieldTypeEnum::TEXT || $type === FormFieldTypeEnum::TEXT_AREA)
        {
            $this->changeOptionTypesForText($options);
            $this->configureOptionsForText($this->resolver);
        }
        else if ($type === FormFieldTypeEnum::NUMBER)
        {
            $this->changeOptionTypesForNumber($options);
            $this->configureOptionsForNumber($this->resolver);
        }
        else if ($type === FormFieldTypeEnum::CHOICE)
        {
            $this->changeOptionTypesForChoice($options);
            $this->configureOptionsForChoice($this->resolver);
        }

        return $this->resolver->resolve($options);
    }

    private function changeOptionTypesForText(array &$options): void
    {
        if (array_key_exists('length_min', $options) && $options['length_min'] !== null && !is_int($options['length_min']))
        {
            $options['length_min'] = (int) $options['length_min'];
        }

        if (array_key_exists('length_max', $options) && $options['length_max'] !== null && !is_int($options['length_max']))
        {
            $options['length_max'] = (int) $options['length_max'];
        }

        if (array_key_exists('regex', $options) && $options['regex'] !== null && !is_string($options['regex']))
        {
            $options['regex'] = (string) $options['regex'];
        }
    }

    private function changeOptionTypesForNumber(array &$options): void
    {
        if (array_key_exists('min', $options) && $options['min'] !== null && !is_int($options['min']) && !is_float($options['min']))
        {
            $options['min'] = (float) $options['min'];
        }

        if (array_key_exists('max', $options) && $options['max'] !== null && !is_int($options['max']) && !is_float($options['max']))
        {
            $options['max'] = (float) $options['max'];
        }

        if (array_key_exists('decimal', $options) && !is_bool($options['decimal']))
        {
            $options['decimal'] = (bool) $options['decimal'];
        }
    }

    private function changeOptionTypesForChoice(array &$options): void
    {
        if (array_key_exists('multiple', $options) && !is_bool($options['multiple']))
        {
            $options['multiple'] = (bool) $options['multiple'];
        }

        if (array_key_exists('expanded', $options) && !is_bool($options['expanded']))
        {
            $options['expanded'] = (bool) $options['expanded'];
        }

        if (array_key_exists('items', $options) && !is_array($options['items']))
        {
            $options['items'] = [(string) $options['items']];
        }
    }

    private function configureOptionsForText(OptionsResolver $resolver): void
    {
        $resolver->setDefault('length_min', null);
        $resolver->setAllowedTypes('length_min', ['null', 'int']);

        $resolver->setDefault('length_max', null);
        $resolver->setAllowedTypes('length_max', ['null', 'int']);

        $resolver->setDefault('regex', null);
        $resolver->setAllowedTypes('regex', ['null', 'string']);
    }

    private function configureOptionsForNumber(OptionsResolver $resolver): void
    {
        $resolver->setDefault('min', null);
        $resolver->setAllowedTypes('min', ['null', 'int', 'float']);

        $resolver->setDefault('max', null);
        $resolver->setAllowedTypes('max', ['null', 'int', 'float']);

        $resolver->setDefault('decimal', false);
        $resolver->setAllowedTypes('decimal', 'bool');
    }

    private function configureOptionsForChoice(OptionsResolver $resolver): void
    {
        $resolver->setDefault('multiple', false);
        $resolver->setAllowedTypes('multiple', 'bool');

        $resolver->setDefault('expanded', false);
        $resolver->setAllowedTypes('expanded', 'bool');

        $resolver->setDefault('items', []);
        $resolver->setAllowedTypes('items', 'string[]');
    }
}