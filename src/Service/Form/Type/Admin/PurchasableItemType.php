<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\PurchasableItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin purchasable item editing.
 */
class PurchasableItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.admin.purchasable_item.name',
            ])
            ->add('price', MoneyType::class, [
                'attr' => [
                    'min' => 0.0,
                ],
                'html5' => true,
                'label' => 'form.admin.purchasable_item.price',
            ])
            ->add('maxAmount', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
                'label' => 'form.admin.purchasable_item.max_amount',
            ])
            ->add('isGlobal', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.purchasable_item.is_global',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchasableItemData::class,
        ]);
    }
}