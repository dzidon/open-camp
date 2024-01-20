<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\DiscountSiblingConfigData;
use App\Service\Form\Type\Common\CollectionItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin discount sibling config editing.
 */
class DiscountSiblingConfigType extends AbstractType
{
    private string $tax;

    public function __construct(string $tax)
    {
        $this->tax = $tax;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ],
                'required'   => false,
                'label'      => 'form.admin.discount_sibling_config.from',
                'label_attr' => [
                    'class' => 'required-conditional',
                ],
            ])
            ->add('to', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ],
                'required'   => false,
                'label'      => 'form.admin.discount_sibling_config.to',
                'label_attr' => [
                    'class' => 'required-conditional',
                ],
            ])
            ->add('discount', MoneyType::class, [
                'attr' => [
                    'min' => 0.0,
                ],
                'html5'                       => true,
                'label'                       => 'form.admin.discount_sibling_config.discount',
                'help'                        => $this->tax > 0.0 ? 'price_includes_tax' : null,
                'help_translation_parameters' => [
                    'tax' => $this->tax,
                ],
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
            'data_class' => DiscountSiblingConfigData::class,
        ]);
    }
}