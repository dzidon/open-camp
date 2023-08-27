<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampCategoryData;
use App\Model\Entity\CampCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp category editing.
 */
class CampCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.admin.camp_category.name',
            ])
            ->add('urlName', TextType::class, [
                'label' => 'form.admin.camp_category.url_name',
            ])
            ->add('parent', EntityType::class, [
                'class'        => CampCategory::class,
                'choice_label' => function (CampCategory $campCategory) {
                    return $campCategory->getPath();
                },
                'choices'     => $options['choices_camp_categories'],
                'placeholder' => 'form.common.choice.none.female',
                'required'    => false,
                'label'       => 'form.admin.camp_category.parent',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => CampCategoryData::class,
            'choices_camp_categories' => [],
        ]);

        $resolver->setAllowedTypes('choices_camp_categories', ['array']);
    }
}