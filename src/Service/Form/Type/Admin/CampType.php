<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampData;
use App\Model\Entity\CampCategory;
use App\Service\Form\Type\Common\TinymceTextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp editing.
 */
class CampType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.admin.camp.name',
            ])
            ->add('urlName', TextType::class, [
                'label' => 'form.admin.camp.url_name',
            ])
            ->add('ageMin', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ],
                'label' => 'form.admin.camp.age_min',
            ])
            ->add('ageMax', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ],
                'label' => 'form.admin.camp.age_max',
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'form.admin.camp.priority',
            ])
            ->add('isFeatured', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp.is_featured',
            ])
            ->add('isHidden', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp.is_hidden',
            ])
            ->add('campCategory', EntityType::class, [
                'class'        => CampCategory::class,
                'choice_label' => function (CampCategory $campCategory) {
                    return $campCategory->getPath();
                },
                'choices'     => $options['choices_camp_categories'],
                'placeholder' => 'form.common.choice.none.female',
                'required'    => false,
                'label'       => 'form.admin.camp.camp_category',
            ])
            ->add('descriptionShort', TextareaType::class, [
                'required' => false,
                'label'    => 'form.admin.camp.description_short',
            ])
            ->add('descriptionLong', TinymceTextareaType::class, [
                'required'  => false,
                'label'     => 'form.admin.camp.description_long',
            ])
            ->add('isAddressPresent', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp.is_address_present',
                'attr'     => [
                    'data-controller'                      => 'cv--checkbox',
                    'data-action'                          => 'cv--checkbox#updateVisibility',
                    'data-cv--checkbox-cv--content-outlet' => '.address-field-visibility',
                ],
            ])
            ->add('street', TextType::class, [
                'label'      => 'form.admin.camp.street',
                'required'   => false,
                'label_attr' => [
                    'class' => 'required'
                ],
                'row_attr' => [
                    'class'                                   => 'address-field-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '1',
                ],
            ])
            ->add('town', TextType::class, [
                'label'      => 'form.admin.camp.town',
                'required'   => false,
                'label_attr' => [
                    'class' => 'required'
                ],
                'row_attr' => [
                    'class'                                   => 'address-field-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '1',
                ],
            ])
            ->add('zip', TextType::class, [
                'label'      => 'form.admin.camp.zip',
                'required'   => false,
                'label_attr' => [
                    'class' => 'required'
                ],
                'row_attr' => [
                    'class'                                   => 'address-field-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '1',
                ],
            ])
            ->add('country', CountryType::class, [
                'placeholder' => 'form.common.choice.choose',
                'required'    => false,
                'label_attr'  => [
                    'class' => 'required'
                ],
                'row_attr' => [
                    'class'                                   => 'address-field-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '1',
                ],
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
                'label' => 'form.admin.camp.country',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => CampData::class,
            'choices_camp_categories' => [],
        ]);

        $resolver->setAllowedTypes('choices_camp_categories', ['array']);
    }
}