<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\AttachmentConfigData;
use App\Library\Data\Admin\FileExtensionData;
use App\Service\Form\Type\Common\AttachmentConfigRequiredType;
use App\Service\Form\Type\Common\CollectionAddItemButtonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin attachment config editing.
 */
class AttachmentConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.admin.attachment_config.name',
            ])
            ->add('label', TextType::class, [
                'label' => 'form.admin.attachment_config.label',
            ])
            ->add('help', TextType::class, [
                'required' => false,
                'label'    => 'form.admin.attachment_config.help',
            ])
            ->add('maxSize', NumberType::class, [
                'attr' => [
                    'min' => 0.01,
                ],
                'html5' => true,
                'scale' => 2,
                'label' => 'form.admin.attachment_config.max_size',
            ])
            ->add('requiredType', AttachmentConfigRequiredType::class, [
                'placeholder'      => 'form.common.choice.choose',
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
                'label' => 'form.admin.attachment_config.required_type',
            ])
            ->add('isGlobal', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.attachment_config.is_global',
            ])
            ->add('fileExtensionsData', CollectionType::class, [
                'entry_type'    => FileExtensionType::class,
                'label'         => 'form.admin.attachment_config.file_extensions',
                'allow_add'     => true,
                'allow_delete'  => true,
                'entry_options' => [
                    'label'         => false,
                    'remove_button' => true,
                ],
                'prototype_options' => [
                    'remove_button' => true,
                ],
                'prototype_data' => new FileExtensionData(),
            ])
            ->add('addFileExtensionData', CollectionAddItemButtonType::class, [
                'label'           => 'form.admin.attachment_config.add_file_extension',
                'collection_name' => 'fileExtensionsData',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttachmentConfigData::class,
        ]);
    }
}