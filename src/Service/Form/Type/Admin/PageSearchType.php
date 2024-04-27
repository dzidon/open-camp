<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\PageSearchData;
use App\Library\Enum\Search\Data\Admin\PageSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin page search.
 */
class PageSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.admin.page_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => PageSortEnum::class,
                'label'        => 'form.admin.page_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    PageSortEnum::CREATED_AT_DESC => 'form.admin.page_search.sort_by.options.created_at_desc',
                    PageSortEnum::CREATED_AT_ASC  => 'form.admin.page_search.sort_by.options.created_at_asc',
                },
            ])
            ->add('isHidden', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices'     => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.page_search.is_hidden',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => PageSearchData::class,
            'block_prefix'       => 'admin_page_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}