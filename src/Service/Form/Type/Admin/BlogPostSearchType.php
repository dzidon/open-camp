<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\BlogPostSearchData;
use App\Library\Enum\Search\Data\Admin\BlogPostSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin blog post search.
 */
class BlogPostSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.admin.blog_post_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => BlogPostSortEnum::class,
                'label'        => 'form.admin.blog_post_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    BlogPostSortEnum::CREATED_AT_DESC => 'form.admin.blog_post_search.sort_by.options.created_at_desc',
                    BlogPostSortEnum::CREATED_AT_ASC  => 'form.admin.blog_post_search.sort_by.options.created_at_asc',
                    BlogPostSortEnum::VIEW_COUNT_DESC => 'form.admin.blog_post_search.sort_by.options.view_count_desc',
                    BlogPostSortEnum::VIEW_COUNT_ASC  => 'form.admin.blog_post_search.sort_by.options.view_count_asc',
                },
            ])
            ->add('author', UserAutocompleteType::class, [
                'required' => false,
                'label'    => 'form.admin.blog_post_search.author',
            ])
            ->add('isHidden', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices'     => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.blog_post_search.is_hidden',
            ])
            ->add('isPinned', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices'     => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.blog_post_search.is_pinned',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => BlogPostSearchData::class,
            'block_prefix'       => 'admin_blog_post_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}