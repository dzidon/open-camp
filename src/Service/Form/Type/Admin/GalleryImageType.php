<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\GalleryImageData;
use App\Model\Entity\GalleryImageCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin gallery image editing.
 */
class GalleryImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void
            {
                /** @var GalleryImageData $data */
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
                        'label'       => 'form.admin.gallery_image.gallery_image_category',
                        'priority'    => 400,
                    ])
                ;
            }
        );

        $builder
            ->add('isHiddenInGallery', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.gallery_image.is_hidden_in_gallery',
                'priority' => 300,
            ])
            ->add('isInCarousel', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.gallery_image.is_in_carousel',
                'attr'     => [
                    'data-controller'                      => 'cv--checkbox',
                    'data-action'                          => 'cv--checkbox#updateVisibility',
                    'data-cv--checkbox-cv--content-outlet' => '.carousel-field-visibility',
                ],
                'priority' => 200,

            ])
            ->add('carouselPriority', IntegerType::class, [
                'required' => false,
                'row_attr' => [
                    'class'                                   => 'carousel-field-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '1',
                ],
                'label_attr' => [
                    'class' => 'required'
                ],
                'label'    => 'form.admin.gallery_image.carousel_priority',
                'priority' => 100,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GalleryImageData::class,
        ]);
    }
}