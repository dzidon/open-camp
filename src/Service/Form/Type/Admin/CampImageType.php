<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampImageData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp image editing.
 */
class CampImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('priority', IntegerType::class, [
                'label' => 'form.admin.camp_image.priority',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampImageData::class,
        ]);
    }
}