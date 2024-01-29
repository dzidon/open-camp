<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Model\Entity\PurchasableItem;
use App\Service\Form\Type\Common\CollectionItemType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp date purchasable item type.
 */
class CampDatePurchasableItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('purchasableItem', EntityType::class, [
                'class'            => PurchasableItem::class,
                'choice_label'     => 'name',
                'choices'          => $options['choices_purchasable_items'],
                'label'            => 'form.admin.camp_date_purchasable_item.purchasable_item',
                'placeholder'      => 'form.common.choice.choose',
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'form.admin.camp_date_purchasable_item.priority',
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
            'data_class'                => CampDatePurchasableItemData::class,
            'choices_purchasable_items' => [],
        ]);

        $resolver->setAllowedTypes('choices_purchasable_items', PurchasableItem::class . '[]');
    }
}