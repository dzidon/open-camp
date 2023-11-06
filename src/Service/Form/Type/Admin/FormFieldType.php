<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\FormFieldData;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Service\Form\Type\Common\CollectionAddItemButtonType;
use App\Service\Form\Type\Common\FormFieldTypeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Traversable;
use Twig\Environment;

/**
 * Admin form field editing.
 */
class FormFieldType extends AbstractType implements DataMapperInterface
{
    private Environment $twig;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(Environment $twig, UrlGeneratorInterface $urlGenerator)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setDataMapper($this);

        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label'    => 'form.admin.form_field.name',
                'priority' => 1000,
            ])
            ->add('label', TextType::class, [
                'label'    => 'form.admin.form_field.label',
                'priority' => 990,
            ])
            ->add('help', TextType::class, [
                'required' => false,
                'label'    => 'form.admin.form_field.help',
                'priority' => 980,
            ])
            ->add('isRequired', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.form_field.is_required',
                'priority' => 970,
            ])
            ->add('isGlobal', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.form_field.is_global',
                'priority' => 960,
            ])
            ->add('type', FormFieldTypeType::class, [
                'row_attr' => [
                    'data-fd--form-field-type-target' => 'typeRow',
                ],
                'attr' => [
                    'data-fd--form-field-type-target' => 'typeSelect',
                    'data-action'                     => 'fd--form-field-type#loadOptionFields'
                ],
                'placeholder'      => 'form.common.choice.choose',
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
                'label' => 'form.admin.form_field.type',
                'priority' => 950,
            ])
        ;

        $formModifier = function (?FormFieldTypeEnum $type, FormInterface $form)
        {
            if ($type === FormFieldTypeEnum::TEXT || $type === FormFieldTypeEnum::TEXT_AREA)
            {
                $this->addTextDependent($form);
            }
            else if ($type === FormFieldTypeEnum::NUMBER)
            {
                $this->addNumberDependent($form);
            }
            else if ($type === FormFieldTypeEnum::CHOICE)
            {
                $this->addChoiceDependent($form);
            }
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier): void
            {
                 /** @var FormFieldData $data */
                $data = $event->getData();
                $type = $data->getType();
                $form = $event->getForm();

                $formModifier($type, $form);
            }
        );

        $builder->addEventListener(FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier): void
            {
                /** @var FormFieldData $data */
                $data = $event->getData();
                $typeValue = $data['type'];
                $type = FormFieldTypeEnum::tryFrom($typeValue);
                $form = $event->getForm();

                if ($type !== FormFieldTypeEnum::TEXT && $type !== FormFieldTypeEnum::TEXT_AREA)
                {
                    if ($form->has('lengthMin')) $form->remove('lengthMin');
                    if ($form->has('lengthMax')) $form->remove('lengthMax');
                    if ($form->has('regex'))     $form->remove('regex');
                }

                if ($type !== FormFieldTypeEnum::NUMBER)
                {
                    if ($form->has('min'))     $form->remove('min');
                    if ($form->has('max'))     $form->remove('max');
                    if ($form->has('decimal')) $form->remove('decimal');
                }

                if ($type !== FormFieldTypeEnum::CHOICE)
                {
                    if ($form->has('expanded')) $form->remove('expanded');
                    if ($form->has('multiple')) $form->remove('multiple');
                    if ($form->has('items'))    $form->remove('items');
                    if ($form->has('addItem'))  $form->remove('addItem');
                }

                $formModifier($type, $form);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $spinnerHtml = $this->twig->render('_fragment/_loading/_spinner.html.twig');
        $fragmentUrl = $this->urlGenerator->generate('admin_fragment_form_field_type');

        $resolver->setDefaults([
            'data_class'                     => FormFieldData::class,
            'enable_items_option_validation' => false,
            'error_mapping'                  => [
                'options[length_min]' => 'lengthMin',
                'options[length_max]' => 'lengthMax',
                'options[regex]'      => 'regex',
                'options[min]'        => 'min',
                'options[max]'        => 'max',
                'options[decimal]'    => 'decimal',
                'options[expanded]'   => 'expanded',
                'options[multiple]'   => 'multiple',
                // items can't be mapped here, validation is in the sub-form
            ],
            'attr' => [
                'data-controller'                        => 'fd--form-field-type',
                'data-fd--form-field-type-spinner-value' => $spinnerHtml,
                'data-fd--form-field-type-url-value'     => $fragmentUrl,
            ],
        ]);

        $resolver->setAllowedTypes('enable_items_option_validation', ['bool']);
    }

    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        if ($viewData === null)
        {
            return;
        }

        if (!$viewData instanceof FormFieldData)
        {
            throw new UnexpectedTypeException($viewData, FormFieldData::class);
        }

        /** @var FormFieldData $formFieldData */
        /** @var FormInterface[] $forms */
        $formFieldData = $viewData;
        $forms = iterator_to_array($forms);

        if (array_key_exists('name', $forms))       $forms['name']->setData($formFieldData->getName());
        if (array_key_exists('label', $forms))      $forms['label']->setData($formFieldData->getLabel());
        if (array_key_exists('help', $forms))       $forms['help']->setData($formFieldData->getHelp());
        if (array_key_exists('isRequired', $forms)) $forms['isRequired']->setData($formFieldData->isRequired());
        if (array_key_exists('isGlobal', $forms))   $forms['isGlobal']->setData($formFieldData->isGlobal());
        if (array_key_exists('type', $forms))       $forms['type']->setData($formFieldData->getType());

        $this->mapTextOptionDataToForms($formFieldData, $forms);
        $this->mapNumberOptionDataToForms($formFieldData, $forms);
        $this->mapChoiceOptionDataToForms($formFieldData, $forms);
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        /** @var FormFieldData $formFieldData */
        /** @var FormInterface[] $forms */
        $formFieldData = $viewData;
        $forms = iterator_to_array($forms);

        if (array_key_exists('name', $forms))       $formFieldData->setName($forms['name']->getData());
        if (array_key_exists('label', $forms))      $formFieldData->setLabel($forms['label']->getData());
        if (array_key_exists('help', $forms))       $formFieldData->setHelp($forms['help']->getData());
        if (array_key_exists('isRequired', $forms)) $formFieldData->setIsRequired($forms['isRequired']->getData());
        if (array_key_exists('isGlobal', $forms))   $formFieldData->setIsGlobal($forms['isGlobal']->getData());
        if (array_key_exists('type', $forms))       $formFieldData->setType($forms['type']->getData());

        $this->mapTextOptionFormsToData($formFieldData, $forms);
        $this->mapNumberOptionFormsToData($formFieldData, $forms);
        $this->mapChoiceOptionFormsToData($formFieldData, $forms);
    }

    private function addTextDependent(FormInterface $form): void
    {
        $form
            ->add('lengthMin', IntegerType::class, [
                'row_attr' => [
                    'data-fd--form-field-type-target' => 'optionRow',
                ],
                'attr' => [
                    'min' => 0,
                ],
                'required' => false,
                'label'    => 'form.admin.form_field.options.length_min',
                'priority' => 940,
            ])
            ->add('lengthMax', IntegerType::class, [
                'row_attr' => [
                    'data-fd--form-field-type-target' => 'optionRow',
                ],
                'attr' => [
                    'min' => 1,
                ],
                'required' => false,
                'label'    => 'form.admin.form_field.options.length_max',
                'priority' => 930,
            ])
            ->add('regex', TextType::class, [
                'row_attr'   => [
                    'data-fd--form-field-type-target' => 'optionRow',
                ],
                'required' => false,
                'label'    => 'form.admin.form_field.options.regex',
                'priority' => 920,
            ])
        ;
    }

    private function addNumberDependent(FormInterface $form): void
    {
        $form
            ->add('min', NumberType::class, [
                'row_attr'   => [
                    'data-fd--form-field-type-target' => 'optionRow',
                ],
                'html5'    => true,
                'required' => false,
                'label'    => 'form.admin.form_field.options.min',
                'priority' => 940,
            ])
            ->add('max', NumberType::class, [
                'row_attr'   => [
                    'data-fd--form-field-type-target' => 'optionRow',
                ],
                'html5'    => true,
                'required' => false,
                'label'    => 'form.admin.form_field.options.max',
                'priority' => 930,
            ])
            ->add('decimal', CheckboxType::class, [
                'row_attr'   => [
                    'data-fd--form-field-type-target' => 'optionRow',
                ],
                'required' => false,
                'label'    => 'form.admin.form_field.options.decimal',
                'priority' => 920,
            ])
        ;
    }

    private function addChoiceDependent(FormInterface $form): void
    {
        $enableItemsOptionValidation = $form->getConfig()
            ->getOption('enable_items_option_validation')
        ;

        $form
            ->add('multiple', CheckboxType::class, [
                'row_attr'   => [
                    'data-fd--form-field-type-target' => 'optionRow',
                ],
                'required' => false,
                'label'    => 'form.admin.form_field.options.multiple',
                'priority' => 940,
            ])
            ->add('expanded', CheckboxType::class, [
                'row_attr'   => [
                    'data-fd--form-field-type-target' => 'optionRow',
                ],
                'required' => false,
                'label'    => 'form.admin.form_field.options.expanded',
                'priority' => 930,
            ])
            ->add('items', CollectionType::class, [
                'row_attr'   => [
                    'data-fd--form-field-type-target' => 'optionRow',
                ],
                'entry_type'    => FormFieldChoiceItemType::class,
                'label'         => 'form.admin.form_field.options.items',
                'allow_add'     => true,
                'allow_delete'  => true,
                'entry_options' => [
                    'label'             => false,
                    'remove_button'     => true,
                    'enable_validation' => $enableItemsOptionValidation,
                ],
                'prototype_options' => [
                    'remove_button'     => true,
                    'enable_validation' => $enableItemsOptionValidation,
                ],
                'priority' => 920,
            ])
            ->add('addItem', CollectionAddItemButtonType::class, [
                'row_attr'   => [
                    'data-fd--form-field-type-target' => 'optionRow',
                ],
                'label'           => 'form.admin.form_field.options.add_item',
                'collection_name' => 'items',
                'priority'        => 910,
            ])
        ;
    }

    private function mapTextOptionDataToForms(FormFieldData $formFieldData, array $forms): void
    {
        if (!$this->allowTextOptionMapping($formFieldData, $forms))
        {
            return;
        }

        $forms['lengthMin']->setData($formFieldData->getOption('length_min'));
        $forms['lengthMax']->setData($formFieldData->getOption('length_max'));
        $forms['regex']->setData($formFieldData->getOption('regex'));
    }

    private function mapNumberOptionDataToForms(FormFieldData $formFieldData, array $forms): void
    {
        if (!$this->allowNumberOptionMapping($formFieldData, $forms))
        {
            return;
        }

        $forms['min']->setData($formFieldData->getOption('min'));
        $forms['max']->setData($formFieldData->getOption('max'));
        $forms['decimal']->setData($formFieldData->getOption('decimal'));
    }

    private function mapChoiceOptionDataToForms(FormFieldData $formFieldData, array $forms): void
    {
        if (!$this->allowChoiceOptionMapping($formFieldData, $forms))
        {
            return;
        }

        $forms['expanded']->setData($formFieldData->getOption('expanded'));
        $forms['multiple']->setData($formFieldData->getOption('multiple'));
        $forms['items']->setData(array_map(function (string $item) {
            return ['value' => $item];
        }, $formFieldData->getOption('items')));
    }

    private function mapTextOptionFormsToData(FormFieldData $formFieldData, array $forms): void
    {
        if (!$this->allowTextOptionMapping($formFieldData, $forms))
        {
            return;
        }

        $formFieldData->setOption('length_min', $forms['lengthMin']->getData());
        $formFieldData->setOption('length_max', $forms['lengthMax']->getData());
        $formFieldData->setOption('regex', $forms['regex']->getData());
    }

    private function mapNumberOptionFormsToData(FormFieldData $formFieldData, array $forms): void
    {
        if (!$this->allowNumberOptionMapping($formFieldData, $forms))
        {
            return;
        }

        $formFieldData->setOption('min', $forms['min']->getData());
        $formFieldData->setOption('max', $forms['max']->getData());
        $formFieldData->setOption('decimal', $forms['decimal']->getData());
    }

    private function mapChoiceOptionFormsToData(FormFieldData $formFieldData, array $forms): void
    {
        if (!$this->allowChoiceOptionMapping($formFieldData, $forms))
        {
            return;
        }

        $formFieldData->setOption('expanded', $forms['expanded']->getData());
        $formFieldData->setOption('multiple', $forms['multiple']->getData());
        $formFieldData->setOption('items', array_map(function (array $nestedForms) {
            return $nestedForms['value'];
        }, $forms['items']->getData()));
    }

    private function allowTextOptionMapping(FormFieldData $formFieldData, array $forms): bool
    {
        if (!array_key_exists('lengthMin', $forms) || !array_key_exists('lengthMax', $forms) || !array_key_exists('regex', $forms))
        {
            return false;
        }

        if (!$formFieldData->hasOption('length_min') || !$formFieldData->hasOption('length_max') || !$formFieldData->hasOption('regex'))
        {
            return false;
        }

        return true;
    }

    private function allowNumberOptionMapping(FormFieldData $formFieldData, array $forms): bool
    {
        if (!array_key_exists('min', $forms) || !array_key_exists('max', $forms) || !array_key_exists('decimal', $forms))
        {
            return false;
        }

        if (!$formFieldData->hasOption('min') || !$formFieldData->hasOption('max') || !$formFieldData->hasOption('decimal'))
        {
            return false;
        }

        return true;
    }

    private function allowChoiceOptionMapping(FormFieldData $formFieldData, array $forms): bool
    {
        if (!array_key_exists('expanded', $forms) || !array_key_exists('multiple', $forms) || !array_key_exists('items', $forms))
        {
            return false;
        }

        if (!$formFieldData->hasOption('expanded') || !$formFieldData->hasOption('multiple') || !$formFieldData->hasOption('items'))
        {
            return false;
        }

        return true;
    }
}