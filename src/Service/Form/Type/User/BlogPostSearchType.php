<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\BlogPostSearchData;
use App\Library\Enum\Search\Data\User\BlogPostSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User blog post search.
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
                'label'    => 'form.user.blog_post_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => BlogPostSortEnum::class,
                'label'        => 'form.user.blog_post_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    BlogPostSortEnum::CREATED_AT_DESC => 'form.user.blog_post_search.sort_by.options.created_at_desc',
                    BlogPostSortEnum::CREATED_AT_ASC  => 'form.user.blog_post_search.sort_by.options.created_at_asc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => BlogPostSearchData::class,
            'block_prefix'       => 'user_blog_post_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}