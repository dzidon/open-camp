<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationAttachmentsUploadLaterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationAttachmentsUploadLaterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('applicationAttachmentsData', CollectionType::class, [
                'entry_type'    => ApplicationAttachmentsType::class,
                'label'         => false,
                'row_attr'      => [
                    'class' => 'mb-0',
                ],
                'entry_options' => [
                    'label'     => false,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApplicationAttachmentsUploadLaterData::class,
        ]);
    }
}