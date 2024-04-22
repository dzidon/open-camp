<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\GalleryImagesUploadData;
use App\Model\Entity\GalleryImageCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin gallery image editing.
 */
class GalleryImagesUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void
            {
                /** @var GalleryImagesUploadData $data */
                $data = $event->getData();
                $categories = $data->getGalleryImageCategories();
                $form = $event->getForm();

                $form
                    ->add('galleryImageCategory', EntityType::class, [
                        'class'        => GalleryImageCategory::class,
                        'choice_label' => function (GalleryImageCategory $galleryImageCategory): string
                        {
                            return $galleryImageCategory->getPath();
                        },
                        'choices'     => $categories,
                        'placeholder' => 'form.common.choice.none.female',
                        'required'    => false,
                        'label'       => 'form.admin.gallery_images_upload.gallery_image_category',
                        'priority'    => 300,
                    ])
                ;
            }
        );

        $builder
            ->add('images', FileType::class, [
                'multiple' => true,
                'label'    => 'form.admin.camp_images_upload.images',
                'priority' => 400,
            ])
            ->add('isHiddenInGallery', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.gallery_images_upload.is_hidden_in_gallery',
                'priority' => 200,
            ])
            ->add('isInCarousel', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.gallery_images_upload.is_in_carousel',
                'priority' => 100,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GalleryImagesUploadData::class,
        ]);
    }
}