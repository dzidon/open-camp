<?php

namespace App\Service\Form\Type\Common;

use App\Library\Enum\Search\Data\Admin\ApplicationAcceptedStateEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Application accepted enum type.
 */
class ApplicationAcceptedStateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'        => ApplicationAcceptedStateEnum::class,
            'choice_label' => fn ($choice) => match ($choice)
            {
                ApplicationAcceptedStateEnum::UNSETTLED => 'form.common.application_accepted_state.unsettled',
                ApplicationAcceptedStateEnum::ACCEPTED  => 'form.common.application_accepted_state.accepted',
                ApplicationAcceptedStateEnum::DECLINED  => 'form.common.application_accepted_state.declined',
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}