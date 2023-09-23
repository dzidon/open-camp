<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampCreationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp creation.
 */
class CampCreationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campData', CampType::class, [
                'choices_camp_categories' => $options['choices_camp_categories'],
                'label'                   => false,
            ])
            ->add('images', FileType::class, [
                'required' => false,
                'multiple' => true,
                'label'    => 'form.admin.camp_creation.images',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => CampCreationData::class,
            'choices_camp_categories' => [],
        ]);

        $resolver->setAllowedTypes('choices_camp_categories', ['array']);
    }
}