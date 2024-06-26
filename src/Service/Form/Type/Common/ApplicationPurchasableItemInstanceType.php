<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\User\ApplicationPurchasableItemInstanceData as UserApplicationPurchasableItemInstanceData;
use App\Library\Data\Admin\ApplicationPurchasableItemInstanceData as AdminApplicationPurchasableItemInstanceData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\Uuid;

class ApplicationPurchasableItemInstanceType extends AbstractType
{
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $searchedClassName = 'value-visibility';
        $uid = (Uuid::v4())->toRfc4122();
        $name = $uid . '-' . $form->getName();

        $newClassName = $searchedClassName . '-' . $name;
        $amount = $view->children['amount'];
        $applicationPurchasableItemVariants = $view->children['applicationPurchasableItemVariantsData'];
        $valueWrappers = $applicationPurchasableItemVariants->children;

        // amount
        if (array_key_exists('attr', $amount->vars) && array_key_exists('data-cv--checkbox-cv--content-outlet', $amount->vars['attr']))
        {
            $amount->vars['attr']['data-cv--checkbox-cv--content-outlet'] = str_replace(
                $searchedClassName,
                $newClassName,
                $amount->vars['attr']['data-cv--checkbox-cv--content-outlet']
            );
        }

        // values
        foreach ($valueWrappers as $valueWrapper)
        {
            $value = $valueWrapper->children['value'];

            if (!array_key_exists('row_attr', $value->vars))
            {
                continue;
            }

            $rowAttr = $value->vars['row_attr'];

            if (!array_key_exists('class', $rowAttr))
            {
                continue;
            }

            $valueWrapper->children['value']->vars['row_attr']['class'] = str_replace(
                $searchedClassName,
                $newClassName,
                $valueWrapper->children['value']->vars['row_attr']['class']
            );
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var callable $emptyData */
        $emptyData = $options['empty_data'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($emptyData): void
        {
            /** @var null|UserApplicationPurchasableItemInstanceData|AdminApplicationPurchasableItemInstanceData $data */
            $data = $event->getData();

            if ($data === null)
            {
                $data = $emptyData();
                $event->setData($data);
            }

            $form = $event->getForm();

            if ($data->getMaxAmount() <= 1)
            {
                $form
                    ->add('amount', IntegerCheckboxType::class, [
                        'required' => false,
                        'label'    => 'form.common.application_purchasable_item_instance.buy',
                        'attr'     => [
                            'data-controller'                      => 'cv--checkbox',
                            'data-action'                          => 'cv--checkbox#updateVisibility',
                            'data-cv--checkbox-cv--content-outlet' => '.value-visibility',
                        ],
                        'priority' => 200,
                    ])
                ;
            }
            else
            {
                $attr = [
                    'min' => 0,
                    'max' => $data->getMaxAmount(),
                ];

                $form
                    ->add('amount', IntegerType::class, [
                        'attr'     => $attr,
                        'required' => false,
                        'label'    => 'form.common.application_purchasable_item_instance.amount',
                        'priority' => 200,
                    ])
                ;
            }
        });

        $builder
            ->add('applicationPurchasableItemVariantsData', CollectionType::class, [
                'entry_type'    => ApplicationPurchasableItemVariantType::class,
                'entry_options' => [
                    'row_attr'  => [
                        'class' => 'm-0',
                    ],
                ],
                'row_attr' => [
                    'class' => 'm-0',
                ],
                'label'    => false,
                'priority' => 100,
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
            'label' => false,
        ]);

        $resolver->setRequired('empty_data');
    }
}