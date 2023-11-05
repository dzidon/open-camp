<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Model\Entity\AttachmentConfig;
use App\Service\Form\Type\Common\CollectionItemType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Admin camp date attachment config type.
 */
class CampDateAttachmentConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('attachmentConfig', EntityType::class, [
                'class'            => AttachmentConfig::class,
                'choice_label'     => 'name',
                'choices'          => $options['choices_attachment_configs'],
                'label'            => 'form.admin.camp_date_attachment_config.attachment_config',
                'placeholder'      => 'form.common.choice.choose',
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'form.admin.camp_date_attachment_config.priority',
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
            'data_class'                 => CampDateAttachmentConfigData::class,
            'choices_attachment_configs' => [],
        ]);

        $resolver->setAllowedTypes('choices_attachment_configs', ['array']);
    }
}