<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationCamperSearchData;
use App\Library\Enum\Search\Data\Admin\ApplicationCamperSortEnum;
use App\Service\Form\Type\Common\ApplicationAcceptedStateType;
use App\Service\Form\Type\Common\GenderChildishType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin application camper search.
 */
class ApplicationCamperSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void
            {
                /** @var ApplicationCamperSearchData $data */
                $data = $event->getData();
                $form = $event->getForm();

                if (!$data->isEnabledApplicationAcceptedSearch())
                {
                    return;
                }

                $form
                    ->add('isApplicationAccepted', ApplicationAcceptedStateType::class, [
                        'label'        => 'form.admin.application_camper_search.is_accepted',
                        'placeholder'  => 'form.common.choice.irrelevant',
                        'required'     => false,
                    ])
                ;
            }
        );

        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label' => 'form.admin.application_camper_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => ApplicationCamperSortEnum::class,
                'label'        => 'form.admin.application_camper_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    ApplicationCamperSortEnum::CREATED_AT_DESC => 'form.admin.application_camper_search.sort_by.options.created_at_desc',
                    ApplicationCamperSortEnum::CREATED_AT_ASC  => 'form.admin.application_camper_search.sort_by.options.created_at_asc',
                    ApplicationCamperSortEnum::BORN_AT_DESC    => 'form.admin.application_camper_search.sort_by.options.born_at_desc',
                    ApplicationCamperSortEnum::BORN_AT_ASC     => 'form.admin.application_camper_search.sort_by.options.born_at_asc',
                },
            ])
            ->add('ageMin', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ],
                'required' => false,
                'label'    => 'form.admin.application_camper_search.age_min',
            ])
            ->add('ageMax', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                ],
                'required' => false,
                'label'    => 'form.admin.application_camper_search.age_max',
            ])
            ->add('gender', GenderChildishType::class, [
                'expanded'    => false,
                'required'    => false,
                'placeholder' => 'form.common.choice.irrelevant',
                'label'       => 'form.admin.application_camper_search.gender',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => ApplicationCamperSearchData::class,
            'block_prefix'       => 'admin_application_camper_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}