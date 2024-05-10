<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\GalleryImageCategoryTruncateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin gallery image category truncate type.
 */
class GalleryImageCategoryTruncateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('offspringsToo', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.gallery_image_category_truncate.offsprings_too',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GalleryImageCategoryTruncateData::class,
        ]);
    }
}