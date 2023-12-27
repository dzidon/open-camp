<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\PurchasableItemSearchData;
use App\Library\Enum\Search\Data\Admin\PurchasableItemSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin purchasable item search.
 */
class PurchasableItemSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.admin.purchasable_item_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => PurchasableItemSortEnum::class,
                'label'        => 'form.admin.purchasable_item_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice) {
                    PurchasableItemSortEnum::CREATED_AT_DESC => 'form.admin.purchasable_item_search.sort_by.options.created_at_desc',
                    PurchasableItemSortEnum::CREATED_AT_ASC  => 'form.admin.purchasable_item_search.sort_by.options.created_at_asc',
                    PurchasableItemSortEnum::NAME_ASC        => 'form.admin.purchasable_item_search.sort_by.options.name_asc',
                    PurchasableItemSortEnum::NAME_DESC       => 'form.admin.purchasable_item_search.sort_by.options.name_desc',
                    PurchasableItemSortEnum::PRICE_ASC       => 'form.admin.purchasable_item_search.sort_by.options.price_asc',
                    PurchasableItemSortEnum::PRICE_DESC      => 'form.admin.purchasable_item_search.sort_by.options.price_desc',
                    PurchasableItemSortEnum::MAX_AMOUNT_ASC  => 'form.admin.purchasable_item_search.sort_by.options.max_amount_asc',
                    PurchasableItemSortEnum::MAX_AMOUNT_DESC => 'form.admin.purchasable_item_search.sort_by.options.max_amount_desc',
                },
            ])
            ->add('isGlobal', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices' => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.purchasable_item_search.is_global',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => PurchasableItemSearchData::class,
            'block_prefix'       => 'admin_purchasable_item_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}