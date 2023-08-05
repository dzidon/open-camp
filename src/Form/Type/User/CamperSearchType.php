<?php

namespace App\Form\Type\User;

use App\Enum\Search\Data\User\CamperSortEnum;
use App\Form\DataTransfer\Data\User\CamperSearchDataInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User camper search.
 */
class CamperSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label'    => 'form.user.camper_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => CamperSortEnum::class,
                'label'        => 'form.user.camper_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice) {
                    CamperSortEnum::CREATED_AT_DESC => 'form.user.camper_search.sort_by.options.created_at_desc',
                    CamperSortEnum::CREATED_AT_ASC  => 'form.user.camper_search.sort_by.options.created_at_asc',
                    CamperSortEnum::NAME_LAST_ASC   => 'form.user.camper_search.sort_by.options.name_last_asc',
                    CamperSortEnum::NAME_LAST_DESC  => 'form.user.camper_search.sort_by.options.name_last_desc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => CamperSearchDataInterface::class,
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}