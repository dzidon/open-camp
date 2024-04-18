<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\GalleryImageCategoryData;
use App\Model\Entity\GalleryImageCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin gallery image category editing.
 */
class GalleryImageCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.admin.gallery_image_category.name',
            ])
            ->add('urlName', TextType::class, [
                'label' => 'form.admin.gallery_image_category.url_name',
            ])
            ->add('parent', EntityType::class, [
                'class'        => GalleryImageCategory::class,
                'choice_label' => function (GalleryImageCategory $galleryImageCategory): string
                {
                    return $galleryImageCategory->getPath();
                },
                'choices'     => $options['choices_gallery_image_categories'],
                'placeholder' => 'form.common.choice.none.female',
                'required'    => false,
                'label'       => 'form.admin.gallery_image_category.parent',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'                       => GalleryImageCategoryData::class,
            'choices_gallery_image_categories' => [],
        ]);

        $resolver->setAllowedTypes('choices_gallery_image_categories', GalleryImageCategory::class . '[]');
    }
}