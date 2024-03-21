<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Library\Data\User\ApplicationPurchasableItemVariantData;
use App\Service\Form\Type\Common\CollectionItemType;
use App\Service\Form\Type\Common\IntegerCheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\UuidV4;

class ApplicationPurchasableItemInstanceType extends AbstractType
{
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $searchedClassName = 'value-visibility';
        $name = (new UuidV4())->toRfc4122();

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
        /** @var ApplicationPurchasableItemInstanceData $defaultData */
        $defaultData = $options['empty_data'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($defaultData): void
        {
            if ($event->getData() === null)
            {
                $clonedDefaultData = $this->cloneDefaultApplicationPurchasableItemInstanceData($defaultData);
                $event->setData($clonedDefaultData);
            }

            /** @var ApplicationPurchasableItemInstanceData $data */
            $data = $event->getData();
            $form = $event->getForm();

            if ($data->getMaxAmount() <= 1)
            {
                $form
                    ->add('amount', IntegerCheckboxType::class, [
                        'required' => false,
                        'label'    => 'form.user.application_purchasable_item_instance.buy',
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
                        'label'    => 'form.user.application_purchasable_item_instance.amount',
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
            'data_class' => ApplicationPurchasableItemInstanceData::class,
            'label'      => false,
        ]);
    }

    private function cloneDefaultApplicationPurchasableItemInstanceData(ApplicationPurchasableItemInstanceData $data): ApplicationPurchasableItemInstanceData
    {
        $newData = new ApplicationPurchasableItemInstanceData($data->getMaxAmount());

        foreach ($data->getApplicationPurchasableItemVariantsData() as $applicationPurchasableItemVariantData)
        {
            $newApplicationPurchasableItemVariantData = new ApplicationPurchasableItemVariantData(
                $applicationPurchasableItemVariantData->getLabel(),
                $applicationPurchasableItemVariantData->getValidValues(),
            );

            $newData->addApplicationPurchasableItemVariantsDatum($newApplicationPurchasableItemVariantData);
        }

        return $newData;
    }
}