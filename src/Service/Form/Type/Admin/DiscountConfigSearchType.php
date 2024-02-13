<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\DiscountConfigSearchData;
use App\Library\Enum\Search\Data\Admin\DiscountConfigSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin sale config search.
 */
class DiscountConfigSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.admin.discount_config_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => DiscountConfigSortEnum::class,
                'label'        => 'form.admin.discount_config_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    DiscountConfigSortEnum::CREATED_AT_DESC => 'form.admin.discount_config_search.sort_by.options.created_at_desc',
                    DiscountConfigSortEnum::CREATED_AT_ASC  => 'form.admin.discount_config_search.sort_by.options.created_at_asc',
                    DiscountConfigSortEnum::NAME_ASC        => 'form.admin.discount_config_search.sort_by.options.name_asc',
                    DiscountConfigSortEnum::NAME_DESC       => 'form.admin.discount_config_search.sort_by.options.name_desc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => DiscountConfigSearchData::class,
            'block_prefix'            => 'admin_discount_config_search',
            'csrf_protection'         => false,
            'method'                  => 'GET',
            'allow_extra_fields'      => true,
        ]);
    }
}