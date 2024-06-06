<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\DownloadableFileSearchData;
use App\Library\Enum\Search\Data\Admin\DownloadableFileSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin downloadable file search.
 */
class DownloadableFileSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void
        {
            /** @var DownloadableFileSearchData $data */
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
                    'label'                     => 'form.admin.downloadable_file_search.extensions',
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
                'label'    => 'form.admin.downloadable_file_search.phrase',
                'priority' => 300,
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => DownloadableFileSortEnum::class,
                'label'        => 'form.admin.downloadable_file_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    DownloadableFileSortEnum::CREATED_AT_DESC => 'form.admin.downloadable_file_search.sort_by.options.created_at_desc',
                    DownloadableFileSortEnum::CREATED_AT_ASC  => 'form.admin.downloadable_file_search.sort_by.options.created_at_asc',
                    DownloadableFileSortEnum::TITLE_ASC       => 'form.admin.downloadable_file_search.sort_by.options.title_asc',
                    DownloadableFileSortEnum::TITLE_DESC      => 'form.admin.downloadable_file_search.sort_by.options.title_desc',
                    DownloadableFileSortEnum::PRIORITY_DESC   => 'form.admin.downloadable_file_search.sort_by.options.priority_desc',
                    DownloadableFileSortEnum::PRIORITY_ASC    => 'form.admin.downloadable_file_search.sort_by.options.priority_asc',
                },
                'priority' => 200,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => DownloadableFileSearchData::class,
            'block_prefix'       => 'admin_downloadable_file_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}