<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\AttachmentConfigSearchData;
use App\Library\Enum\Search\Data\Admin\AttachmentConfigSortEnum;
use App\Model\Entity\FileExtension;
use App\Service\Form\Type\Common\AttachmentConfigRequiredType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin attachment config search.
 */
class AttachmentConfigSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label' => 'form.admin.attachment_config_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => AttachmentConfigSortEnum::class,
                'label'        => 'form.admin.attachment_config_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice) {
                    AttachmentConfigSortEnum::CREATED_AT_DESC => 'form.admin.attachment_config_search.sort_by.options.created_at_desc',
                    AttachmentConfigSortEnum::CREATED_AT_ASC  => 'form.admin.attachment_config_search.sort_by.options.created_at_asc',
                    AttachmentConfigSortEnum::NAME_ASC        => 'form.admin.attachment_config_search.sort_by.options.name_asc',
                    AttachmentConfigSortEnum::NAME_DESC       => 'form.admin.attachment_config_search.sort_by.options.name_desc',
                    AttachmentConfigSortEnum::MAX_SIZE_ASC    => 'form.admin.attachment_config_search.sort_by.options.max_size_asc',
                    AttachmentConfigSortEnum::MAX_SIZE_DESC   => 'form.admin.attachment_config_search.sort_by.options.max_size_desc',
                },
            ])
            ->add('fileExtensions', EntityType::class, [
                'class'        => FileExtension::class,
                'choices'      => $options['choices_file_extensions'],
                'choice_label' => 'extension',
                'choice_value' => 'extension',
                'multiple'     => true,
                'autocomplete' => true,
                'required'     => false,
                'label'        => 'form.admin.attachment_config_search.file_extensions',
            ])
            ->add('requiredType', AttachmentConfigRequiredType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'required'    => false,
                'label'       => 'form.admin.attachment_config_search.required_type',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => AttachmentConfigSearchData::class,
            'csrf_protection'         => false,
            'method'                  => 'GET',
            'allow_extra_fields'      => true,
            'choices_file_extensions' => [],
        ]);

        $resolver->setAllowedTypes('choices_file_extensions', ['array']);
    }
}