<?php

namespace App\Service\Form\Type\Common;

use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Attachment config required enum type.
 */
class AttachmentConfigRequiredType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'        => AttachmentConfigRequiredTypeEnum::class,
            'expanded'     => false,
            'multiple'     => false,
            'choice_label' => fn ($choice) => match ($choice) {
                AttachmentConfigRequiredTypeEnum::OPTIONAL       => 'attachment_config_required_type.optional',
                AttachmentConfigRequiredTypeEnum::REQUIRED       => 'attachment_config_required_type.required',
                AttachmentConfigRequiredTypeEnum::REQUIRED_LATER => 'attachment_config_required_type.required_later',
            },
        ]);
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}