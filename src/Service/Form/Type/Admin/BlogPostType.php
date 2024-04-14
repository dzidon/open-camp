<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\BlogPostData;
use App\Service\Form\Type\Common\TinymceTextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin blog post editing.
 */
class BlogPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr'  => ['autofocus' => 'autofocus'],
                'label' => 'form.admin.blog_post.title',
            ])
            ->add('urlName', TextType::class, [
                'label' => 'form.admin.blog_post.url_name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'form.admin.blog_post.description',
            ])
            ->add('content', TinymceTextareaType::class, [
                'required'   => false,
                'label'      => 'form.admin.blog_post.content',
                'label_attr' => ['class' => 'required'],
            ])
            ->add('isHidden', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.blog_post.is_hidden',
            ])
            ->add('isPinned', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.blog_post.is_pinned',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPostData::class,
        ]);
    }
}