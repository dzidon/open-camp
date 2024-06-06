<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\DownloadableFileCreateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin downloadable file upload.
 */
class DownloadableFileCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr'  => ['autofocus' => 'autofocus'],
                'label' => 'form.admin.downloadable_file_create.title',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label'    => 'form.admin.downloadable_file_create.description',
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'form.admin.downloadable_file_create.priority',
            ])
            ->add('file', FileType::class, [
                'multiple' => false,
                'label'    => 'form.admin.downloadable_file_create.file',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DownloadableFileCreateData::class,
        ]);
    }
}