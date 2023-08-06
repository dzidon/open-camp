<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampDataInterface;
use App\Model\Entity\CampCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp editing.
 */
class CampType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.admin.camp.name',
            ])
            ->add('urlName', TextType::class, [
                'label' => 'form.admin.camp.url_name',
            ])
            ->add('ageMin', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
                'label' => 'form.admin.camp.age_min',
            ])
            ->add('ageMax', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
                'label' => 'form.admin.camp.age_max',
            ])
            ->add('descriptionShort', TextareaType::class, [
                'required' => false,
                'label'    => 'form.admin.camp.description_short',
            ])
            ->add('descriptionLong', TextareaType::class, [
                'required' => false,
                'label'    => 'form.admin.camp.description_long',
                'attr'     => [
                    'class' => 'tinymce'
                ],
            ])
            ->add('campCategory', EntityType::class, [
                'class'        => CampCategory::class,
                'choice_label' => function (CampCategory $campCategory) {
                    return $campCategory->getPath();
                },
                'choices'     => $options['choices_camp_categories'],
                'placeholder' => 'form.common.choice.none.female',
                'required'    => false,
                'label'       => 'form.admin.camp.camp_category',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => CampDataInterface::class,
            'choices_camp_categories' => [],
        ]);

        $resolver->setAllowedTypes('choices_camp_categories', ['array']);
    }
}