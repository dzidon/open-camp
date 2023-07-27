<?php

namespace App\Form\Type\Admin;

use App\Form\DataTransfer\Data\Admin\CampCategoryDataInterface;
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
        $campCategories = $options['choices_camp_categories'];
        $sortPaths = $options['sort_paths'];
        $campCategoryPaths = [];

        /** @var CampCategory $campCategory */
        foreach ($campCategories as $campCategory)
        {
            $path = $campCategory->getPath();
            $campCategoryId = $campCategory->getId();
            $campCategoryPaths[$campCategoryId->toRfc4122()] = $path;
        }

        if ($sortPaths)
        {
            usort($campCategories, function (CampCategory $campCategoryA, CampCategory $campCategoryB) use ($campCategoryPaths)
            {
                $idA = $campCategoryA->getId();
                $idB = $campCategoryB->getId();

                return $campCategoryPaths[$idA->toRfc4122()] <=> $campCategoryPaths[$idB->toRfc4122()];
            });
        }

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
                'choice_label' => function (CampCategory $campCategory) use ($campCategoryPaths) {
                    $campCategoryId = $campCategory->getId();
                    return $campCategoryPaths[$campCategoryId->toRfc4122()];
                },
                'choices'     => $campCategories,
                'placeholder' => 'form.common.choice.none.female',
                'required'    => false,
                'label'       => 'form.admin.camp_category.parent',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => CampCategoryDataInterface::class,
            'choices_camp_categories' => [],
            'sort_paths'              => true,
        ]);

        $resolver->setAllowedTypes('choices_camp_categories', ['array']);
        $resolver->setAllowedTypes('sort_paths', ['bool']);
    }
}