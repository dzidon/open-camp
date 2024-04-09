<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\ApplicationAdminAttachmentUpdateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Application admin attachment creation.
 */
class ApplicationAdminAttachmentUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'attr'  => ['autofocus' => 'autofocus'],
                'label' => 'form.admin.application_admin_attachment_update.label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApplicationAdminAttachmentUpdateData::class,
        ]);
    }
}