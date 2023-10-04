<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Service\Form\Type\Common\CollectionItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin purchasable item variant value editing.
 */
class PurchasableItemVariantValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.admin.purchasable_item_variant_value.name',
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'form.admin.purchasable_item_variant_value.priority',
            ])
        ;
    }

    public function getParent(): string
    {
        return CollectionItemType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchasableItemVariantValueData::class,
        ]);
    }
}