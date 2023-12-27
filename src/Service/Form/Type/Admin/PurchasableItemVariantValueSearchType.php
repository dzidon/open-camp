<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\PurchasableItemVariantValueSearchData;
use App\Library\Enum\Search\Data\Admin\PurchasableItemVariantValueSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin purchasable item variant value search.
 */
class PurchasableItemVariantValueSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.admin.purchasable_item_variant_value_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => PurchasableItemVariantValueSortEnum::class,
                'label'        => 'form.admin.purchasable_item_variant_value_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice) {
                    PurchasableItemVariantValueSortEnum::CREATED_AT_DESC => 'form.admin.purchasable_item_variant_value_search.sort_by.options.created_at_desc',
                    PurchasableItemVariantValueSortEnum::CREATED_AT_ASC  => 'form.admin.purchasable_item_variant_value_search.sort_by.options.created_at_asc',
                    PurchasableItemVariantValueSortEnum::NAME_ASC        => 'form.admin.purchasable_item_variant_value_search.sort_by.options.name_asc',
                    PurchasableItemVariantValueSortEnum::NAME_DESC       => 'form.admin.purchasable_item_variant_value_search.sort_by.options.name_desc',
                    PurchasableItemVariantValueSortEnum::PRIORITY_ASC    => 'form.admin.purchasable_item_variant_value_search.sort_by.options.priority_asc',
                    PurchasableItemVariantValueSortEnum::PRIORITY_DESC   => 'form.admin.purchasable_item_variant_value_search.sort_by.options.priority_desc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => PurchasableItemVariantValueSearchData::class,
            'block_prefix'       => 'admin_purchasable_item_variant_value_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}