<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationAdminAttachmentSearchData;
use App\Library\Enum\Search\Data\Admin\ApplicationAdminAttachmentSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin application admin attachment search.
 */
class ApplicationAdminAttachmentSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void
        {
            /** @var ApplicationAdminAttachmentSearchData $data */
            $data = $event->getData();
            $form = $event->getForm();
            $validExtensions = $data->getValidExtensions();

            $form
                ->add('extensions', ChoiceType::class, [
                    'choices'      => $validExtensions,
                    'choice_label' => function (string $value): string
                    {
                        return $value;
                    },
                    'multiple'                  => true,
                    'autocomplete'              => true,
                    'required'                  => false,
                    'choice_translation_domain' => false,
                    'label'                     => 'form.admin.application_admin_attachment_search.extensions',
                    'priority'                  => 100,
                ])
            ;
        });

        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.admin.application_admin_attachment_search.phrase',
                'priority' => 300,
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => ApplicationAdminAttachmentSortEnum::class,
                'label'        => 'form.admin.application_admin_attachment_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    ApplicationAdminAttachmentSortEnum::CREATED_AT_DESC => 'form.admin.application_admin_attachment_search.sort_by.options.created_at_desc',
                    ApplicationAdminAttachmentSortEnum::CREATED_AT_ASC  => 'form.admin.application_admin_attachment_search.sort_by.options.created_at_asc',
                    ApplicationAdminAttachmentSortEnum::LABEL_ASC       => 'form.admin.application_admin_attachment_search.sort_by.options.label_asc',
                    ApplicationAdminAttachmentSortEnum::LABEL_DESC      => 'form.admin.application_admin_attachment_search.sort_by.options.label_desc',
                },
                'priority' => 200,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => ApplicationAdminAttachmentSearchData::class,
            'block_prefix'       => 'admin_application_admin_attachment_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}