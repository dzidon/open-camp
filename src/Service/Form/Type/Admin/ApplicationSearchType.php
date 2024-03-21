<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationSearchData;
use App\Library\Enum\Search\Data\Admin\ApplicationAcceptedStateEnum;
use App\Library\Enum\Search\Data\Admin\ApplicationSortEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin application search.
 */
class ApplicationSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'required' => false,
                'label' => 'form.admin.application_search.phrase',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => ApplicationSortEnum::class,
                'label'        => 'form.admin.application_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    ApplicationSortEnum::COMPLETED_AT_DESC => 'form.admin.application_search.sort_by.options.completed_at_desc',
                    ApplicationSortEnum::COMPLETED_AT_ASC  => 'form.admin.application_search.sort_by.options.completed_at_asc',
                },
            ])
            ->add('isOnlinePaymentMethod', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'required'    => false,
                'choices'     => [
                    'form.admin.application_search.is_online_payment_method.options.yes' => true,
                    'form.admin.application_search.is_online_payment_method.options.no'  => false,
                ],
                'label' => 'form.admin.application_search.is_online_payment_method.label',
            ])
            ->add('isAccepted', EnumType::class, [
                'class'        => ApplicationAcceptedStateEnum::class,
                'label'        => 'form.admin.application_search.is_accepted.label',
                'placeholder'  => 'form.common.choice.irrelevant',
                'required'     => false,
                'choice_label' => fn ($choice) => match ($choice)
                {
                    ApplicationAcceptedStateEnum::UNSETTLED => 'form.admin.application_search.is_accepted.options.unsettled',
                    ApplicationAcceptedStateEnum::ACCEPTED  => 'form.admin.application_search.is_accepted.options.accepted',
                    ApplicationAcceptedStateEnum::DECLINED  => 'form.admin.application_search.is_accepted.options.declined',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => ApplicationSearchData::class,
            'block_prefix'       => 'admin_application_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}