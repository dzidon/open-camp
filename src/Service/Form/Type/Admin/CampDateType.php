<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampDateData;
use App\Service\Form\Type\Common\CollectionItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp editing.
 */
class CampDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt', DateTimeType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'label'  => 'form.admin.camp_date.start_at',
            ])
            ->add('endAt', DateTimeType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'label'  => 'form.admin.camp_date.end_at',
            ])
            ->add('price', MoneyType::class, [
                'attr' => [
                    'min' => 0.0,
                ],
                'html5' => true,
                'label' => 'form.admin.camp_date.price',
            ])
            ->add('capacity', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                ],
                'label' => 'form.admin.camp_date.capacity',
            ])
            ->add('tripInstructions', TextareaType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date.trip_instructions',
            ])
            ->add('leaders', UserAutocompleteType::class, [
                'required' => false,
                'multiple' => true,
                'label'    => 'form.admin.camp_date.leaders',
            ])
            ->add('isClosed', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date.is_closed',
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
            'data_class' => CampDateData::class,
        ]);
    }
}