<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\TripLocationData;
use App\Service\Form\Type\Common\CollectionItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin trip location editing.
 */
class TripLocationType extends AbstractType
{
    private string $tax;

    public function __construct(string $tax)
    {
        $this->tax = $tax;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.admin.trip_location.name',
            ])
            ->add('price', MoneyType::class, [
                'attr' => [
                    'min' => 0.0,
                ],
                'html5'                       => true,
                'label'                       => 'form.admin.trip_location.price',
                'help'                        => $this->tax > 0.0 ? 'price_includes_tax' : null,
                'help_translation_parameters' => [
                    'tax' => $this->tax,
                ],
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'form.admin.trip_location.priority',
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
            'data_class' => TripLocationData::class,
        ]);
    }
}