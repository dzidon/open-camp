<?php

namespace App\Form\Type\Admin;

use App\Enum\Search\Data\Admin\CampSortEnum;
use App\Form\DataTransfer\Data\Admin\CampSearchDataInterface;
use App\Model\Entity\CampCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp search.
 */
class CampSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label' => 'form.admin.camp_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => CampSortEnum::class,
                'label'        => 'form.admin.camp_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice) {
                    CampSortEnum::CREATED_AT_DESC => 'form.admin.camp_search.sort_by.options.created_at_desc',
                    CampSortEnum::CREATED_AT_ASC  => 'form.admin.camp_search.sort_by.options.created_at_asc',
                    CampSortEnum::NAME_ASC        => 'form.admin.camp_search.sort_by.options.name_asc',
                    CampSortEnum::NAME_DESC       => 'form.admin.camp_search.sort_by.options.name_desc',
                    CampSortEnum::URL_NAME_ASC    => 'form.admin.camp_search.sort_by.options.url_name_asc',
                    CampSortEnum::URL_NAME_DESC   => 'form.admin.camp_search.sort_by.options.url_name_desc',
                },
            ])
            ->add('age', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
                'required' => false,
                'label'    => 'form.admin.camp_search.age',
            ])
            ->add('dateStart', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.admin.camp_search.date_start',
            ])
            ->add('dateEnd', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.admin.camp_search.date_end',
            ])
            ->add('campCategory', EntityType::class, [
                'class'        => CampCategory::class,
                'choice_label' => function (CampCategory $campCategory) {
                    return $campCategory->getPath();
                },
                'choices'     => $options['choices_camp_categories'],
                'placeholder' => 'form.common.choice.irrelevant',
                'required'    => false,
                'label'       => 'form.admin.camp_search.camp_category',
            ])
            ->add('active', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices'  => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.camp_search.active',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => CampSearchDataInterface::class,
            'choices_camp_categories' => [],
            'csrf_protection'         => false,
            'method'                  => 'GET',
            'allow_extra_fields'      => true,
        ]);

        $resolver->setAllowedTypes('choices_camp_categories', ['array']);
    }
}