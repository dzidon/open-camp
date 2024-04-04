<?php

namespace App\Service\Form\Type\Common;

use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Application payment enum type.
 */
class ApplicationPaymentTypeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'        => ApplicationPaymentTypeEnum::class,
            'expanded'     => false,
            'multiple'     => false,
            'choice_label' => fn ($choice) => match ($choice)
            {
                ApplicationPaymentTypeEnum::FULL    => 'application_payment_type.full',
                ApplicationPaymentTypeEnum::DEPOSIT => 'application_payment_type.deposit',
                ApplicationPaymentTypeEnum::REST    => 'application_payment_type.rest',
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}