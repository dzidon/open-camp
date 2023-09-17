<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\FileExtensionData;
use App\Service\Form\Type\Common\CollectionItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin file extension editing.
 */
class FileExtensionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('extension', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => false,
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
            'data_class' => FileExtensionData::class,
        ]);
    }
}