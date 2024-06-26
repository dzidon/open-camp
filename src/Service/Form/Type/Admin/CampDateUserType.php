<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampDateUserData;
use App\Service\Form\Type\Common\CollectionItemType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp date user type.
 */
class CampDateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserAutocompleteType::class, [
                'label' => 'form.admin.camp_date_user.user',
            ])
            ->add('canUpdateApplicationsState', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date_user.can_update_applications_state',
            ])
            ->add('canManageApplications', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date_user.can_manage_applications',
            ])
            ->add('canManageApplicationPayments', CheckboxType::class, [
                'required' => false,
                'label'    => 'form.admin.camp_date_user.can_manage_application_payments',
            ])
        ;
    }

    public function getParent(): string
    {
        return CollectionItemType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CampDateUserData::class,
        ]);
    }
}