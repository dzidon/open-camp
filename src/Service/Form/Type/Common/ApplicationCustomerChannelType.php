<?php

namespace App\Service\Form\Type\Common;

use App\Model\Enum\Entity\ApplicationCustomerChannelEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Application customer channel type.
 */
class ApplicationCustomerChannelType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'        => ApplicationCustomerChannelEnum::class,
            'expanded'     => false,
            'multiple'     => false,
            'choice_label' => function (ApplicationCustomerChannelEnum $choice): string
            {
                return "application_customer_channel.$choice->value";
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}