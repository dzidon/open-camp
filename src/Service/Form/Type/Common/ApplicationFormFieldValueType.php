<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User application form field value edit.
 */
class ApplicationFormFieldValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void
        {
            /** @var ApplicationFormFieldValueData $applicationFormFieldValueData */
            $applicationFormFieldValueData = $event->getData();

            if ($applicationFormFieldValueData === null)
            {
                return;
            }

            $form = $event->getForm();
            $isRequired = $applicationFormFieldValueData->isRequired();
            $label = $applicationFormFieldValueData->getLabel();
            $help = $applicationFormFieldValueData->getHelp();
            $type = $applicationFormFieldValueData->getType();

            $options = [
                'required'           => $isRequired,
                'label'              => $label,
                'help'               => $help,
                'translation_domain' => false,
            ];

            $typeClass = TextType::class;

            if ($type === FormFieldTypeEnum::TEXT_AREA)
            {
                $typeClass = TextareaType::class;
            }
            else if ($type === FormFieldTypeEnum::NUMBER)
            {
                $typeClass = NumberType::class;
                $min = $applicationFormFieldValueData->getOption('min');
                $max = $applicationFormFieldValueData->getOption('max');
                $isDecimal = $applicationFormFieldValueData->getOption('decimal');
                $attr = [];
                $options['scale'] = 0;

                if ($isDecimal)
                {
                    $options['scale'] = 2;
                }

                if ($min !== null)
                {
                    $attr['min'] = $min;
                }

                if ($max !== null)
                {
                    $attr['max'] = $max;
                }

                $options['attr'] = $attr;
                $options['html5'] = true;
            }
            else if ($type === FormFieldTypeEnum::CHOICE)
            {
                $typeClass = ChoiceType::class;
                $multiple = $applicationFormFieldValueData->getOption('multiple');
                $expanded = $applicationFormFieldValueData->getOption('expanded');
                $items = $applicationFormFieldValueData->getOption('items');

                $options['multiple'] = $multiple;
                $options['expanded'] = $expanded;
                $options['choices'] = $items;
                $options['choice_translation_domain'] = false;
                $options['choice_label'] = function (string $choice, string $key, mixed $value): string
                {
                    return $value;
                };
            }

            $form->add('value', $typeClass, $options);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApplicationFormFieldValueData::class,
            'label'      => false,
        ]);
    }
}