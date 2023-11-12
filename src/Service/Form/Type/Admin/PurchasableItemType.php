<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\PurchasableItemData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            ->add('label', TextType::class, [
                'label' => 'form.admin.purchasable_item.label',
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
            ->add('image', FileType::class, [
                'required' => false,
                'multiple' => false,
                'label'    => 'form.admin.purchasable_item.image',
                'row_attr' => [
                    'class'                                  => 'purchasable-item-image',
                    'data-controller'                        => 'cv--content',
                    'data-cv--content-show-when-checked-value' => '0',
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void
            {
                /** @var PurchasableItemData $data */
                $data = $event->getData();
                $purchasableItem = $data->getPurchasableItem();
                $form = $event->getForm();

                if ($purchasableItem === null || $purchasableItem->getImageExtension() === null)
                {
                    return;
                }

                $form
                    ->add('removeImage', CheckboxType::class, [
                        'required' => false,
                        'label'    => 'form.admin.purchasable_item.remove_image',
                        'attr'     => [
                            'data-controller'                    => 'cv--checkbox',
                            'data-action'                        => 'cv--checkbox#updateVisibility',
                            'data-cv--checkbox-cv--content-outlet' => '.purchasable-item-image',
                        ],
                    ])
                ;
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PurchasableItemData::class,
        ]);
    }
}