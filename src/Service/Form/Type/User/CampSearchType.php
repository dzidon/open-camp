<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\CampSearchData;
use App\Library\Enum\Search\Data\User\CampSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User camp catalog search.
 */
class CampSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortBy', EnumType::class, [
                'class'        => CampSortEnum::class,
                'label'        => 'form.user.camp_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice) {
                    CampSortEnum::PRIORITY_DESC         => 'form.user.camp_search.sort_by.options.priority_desc',
                    CampSortEnum::LOWEST_FULL_PRICE_ASC => 'form.user.camp_search.sort_by.options.lowest_full_price_asc',
                    CampSortEnum::LOWEST_START_AT_ASC   => 'form.user.camp_search.sort_by.options.lowest_start_at_asc',
                },
            ])
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.user.camp_search.phrase',
            ])
            ->add('age', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ],
                'required' => false,
                'label'    => 'form.user.camp_search.age',
            ])
            ->add('from', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.user.camp_search.from',
            ])
            ->add('to', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.user.camp_search.to',
            ])
            ->add('isOpenOnly', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.user.camp_search.is_open_only',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => CampSearchData::class,
            'block_prefix'       => 'user_camp_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}