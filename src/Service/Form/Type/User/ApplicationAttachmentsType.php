<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationAttachmentsData;
use App\Service\Form\Type\Common\ApplicationAttachmentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationAttachmentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void
        {
            /** @var ApplicationAttachmentsData $data */
            $data = $event->getData();
            $form = $event->getForm();
            $applicationCamper = $data->getApplicationCamper();
            $label = 'form.user.application_attachments.attachments_global';
            $translationParams = [];

            if ($applicationCamper !== null)
            {
                $translationParams['camper'] = $applicationCamper->getNameFull();
                $label = 'form.user.application_attachments.attachments_camper';
            }

            $form
                ->add('applicationAttachmentsData', CollectionType::class, [
                    'entry_type'                   => ApplicationAttachmentType::class,
                    'label'                        => $label,
                    'label_translation_parameters' => $translationParams,
                    'block_prefix'                 => 'user_application_attachments',
                    'entry_options'                => [
                        'row_attr'  => [
                            'class' => 'm-0',
                        ],
                    ],
                    'row_attr'  => [
                        'class' => 'm-0',
                    ],
                ])
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApplicationAttachmentsData::class,
        ]);
    }
}