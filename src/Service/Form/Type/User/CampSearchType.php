<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\CampSearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User camp catalog search.
 */
class CampSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.user.camp_search.phrase',
            ])
            ->add('age', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ],
                'required' => false,
                'label'    => 'form.user.camp_search.age',
            ])
            ->add('from', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.user.camp_search.from',
                'attr'     => [
                    'data-fd--camp-catalog-search-target' => 'fromInput',
                    'data-action'                         => 'change->fd--camp-catalog-search#onCheckboxChange',
                ],
            ])
            ->add('to', DateType::class, [
                'required' => false,
                'widget'   => 'single_text',
                'input'    => 'datetime_immutable',
                'label'    => 'form.user.camp_search.to',
                'attr'     => [
                    'data-fd--camp-catalog-search-target' => 'toInput',
                    'data-action'                         => 'change->fd--camp-catalog-search#onCheckboxChange',
                ],
            ])
            ->add('isOpenOnly', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.user.camp_search.is_open_only',
                'row_attr' => [
                    'data-fd--camp-catalog-search-target' => 'isOpenOnlyRow',
                ],
                'attr' => [
                    'data-action' => 'change->fd--camp-catalog-search#onCheckboxChange click->fd--camp-catalog-search#onCheckboxClick',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => CampSearchData::class,
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
            'attr'               => [
                'data-controller' => 'fd--camp-catalog-search',
            ],
        ]);
    }
}