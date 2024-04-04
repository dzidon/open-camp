<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationPaymentSearchData;
use App\Library\Enum\Search\Data\Admin\ApplicationPaymentSortEnum;
use App\Service\Form\Type\Common\ApplicationPaymentTypeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin application payment search.
 */
class ApplicationPaymentSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ApplicationPaymentTypeType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'required'    => false,
                'label'       => 'form.admin.application_payment_search.type',
            ])
            ->add('isOnline', ChoiceType::class, [
                'placeholder' => 'form.common.choice.irrelevant',
                'required'    => false,
                'choices'     => [
                    'form.common.choice.yes' => true,
                    'form.common.choice.no'  => false,
                ],
                'label' => 'form.admin.application_payment_search.is_online',
            ])
            ->add('sortBy', EnumType::class, [
                'class'        => ApplicationPaymentSortEnum::class,
                'label'        => 'form.admin.application_payment_search.sort_by.label',
                'choice_label' => fn ($choice) => match ($choice)
                {
                    ApplicationPaymentSortEnum::CREATED_AT_DESC => 'form.admin.application_payment_search.sort_by.options.created_at_desc',
                    ApplicationPaymentSortEnum::CREATED_AT_ASC  => 'form.admin.application_payment_search.sort_by.options.created_at_asc',
                    ApplicationPaymentSortEnum::AMOUNT_ASC      => 'form.admin.application_payment_search.sort_by.options.amount_asc',
                    ApplicationPaymentSortEnum::AMOUNT_DESC     => 'form.admin.application_payment_search.sort_by.options.amount_desc',
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => ApplicationPaymentSearchData::class,
            'block_prefix'       => 'admin_application_payment_search',
            'csrf_protection'    => false,
            'method'             => 'GET',
            'allow_extra_fields' => true,
        ]);
    }
}