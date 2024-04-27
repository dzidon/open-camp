<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\PageData;
use App\Service\Form\Type\Common\TinymceTextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin page editing.
 */
class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr'  => ['autofocus' => 'autofocus'],
                'label' => 'form.admin.page.title',
            ])
            ->add('urlName', TextType::class, [
                'label' => 'form.admin.page.url_name',
            ])
            ->add('content', TinymceTextareaType::class, [
                'required'   => false,
                'label'      => 'form.admin.page.content',
                'label_attr' => ['class' => 'required'],
            ])
            ->add('isHidden', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.page.is_hidden',
            ])
            ->add('isInMenu', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.page.is_in_menu',
                'attr'     => [
                    'data-controller'                      => 'cv--checkbox',
                    'data-action'                          => 'cv--checkbox#updateVisibility',
                    'data-cv--checkbox-cv--content-outlet' => '.menu-field-visibility',
                ],
            ])
            ->add('menuPriority', IntegerType::class, [
                'required' => false,
                'row_attr' => [
                    'class'                                   => 'menu-field-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '1',
                ],
                'label_attr' => [
                    'class' => 'required'
                ],
                'label' => 'form.admin.page.menu_priority',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageData::class,
        ]);
    }
}