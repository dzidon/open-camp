<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\GalleryImageSearchData;
use App\Library\Enum\Search\Data\Admin\GalleryImageSortEnum;
use App\Model\Entity\GalleryImageCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Admin gallery image search.
 */
class GalleryImageSearchType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        array_unshift($options['choices_gallery_image_categories'], false);

        $builder
            ->add('galleryImageCategory', ChoiceType::class, [
                'choices'      => $options['choices_gallery_image_categories'],
                'choice_label' => function (false|GalleryImageCategory $galleryImageCategory): string
                {
                    if ($galleryImageCategory === false)
                    {
                        return $this->translator->trans('search.item_no_reference.female');
                    }

                    return $galleryImageCategory->getPath();
                },
                'placeholder'               => 'form.common.choice.irrelevant',
                'required'                  => false,
                'label'                     => 'form.admin.gallery_image_search.gallery_image_category',
                'choice_translation_domain' => false,
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => GalleryImageSortEnum::class,
                'label'        => 'form.admin.gallery_image_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    GalleryImageSortEnum::PRIORITY_DESC => 'form.admin.gallery_image_search.sort_by.options.priority_desc',
                    GalleryImageSortEnum::PRIORITY_ASC  => 'form.admin.gallery_image_search.sort_by.options.priority_asc',
                },
            ])
            ->add('isHiddenInGallery', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices'     => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.gallery_image_search.is_hidden_in_gallery',
            ])
            ->add('isInCarousel', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'choices'     => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'required' => false,
                'label'    => 'form.admin.gallery_image_search.is_in_carousel',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'                       => GalleryImageSearchData::class,
            'block_prefix'                     => 'admin_gallery_image_search',
            'choices_gallery_image_categories' => [],
            'csrf_protection'                  => false,
            'method'                           => 'GET',
            'allow_extra_fields'               => true,
        ]);

        $resolver->setAllowedTypes('choices_gallery_image_categories', ['array']);
    }
}