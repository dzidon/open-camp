<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationAttachmentData;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * User application attachment edit.
 */
class ApplicationAttachmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void
        {
            /** @var null|ApplicationAttachmentData $applicationAttachmentData */
            $applicationAttachmentData = $event->getData();
            $form = $event->getForm();

            if ($applicationAttachmentData === null)
            {
                return;
            }

            $requiredType = $applicationAttachmentData->getRequiredType();
            $isAlreadyUploaded = $applicationAttachmentData->isAlreadyUploaded();
            $isRequired = $requiredType === AttachmentConfigRequiredTypeEnum::REQUIRED && !$isAlreadyUploaded;
            $label = $applicationAttachmentData->getLabel();

            $form
                ->add('file', FileType::class, [
                    'block_prefix'       => 'user_application_attachment_file',
                    'multiple'           => false,
                    'required'           => $isRequired,
                    'label'              => $label,
                    'translation_domain' => false,
                ])
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApplicationAttachmentData::class,
            'label'      => false,
        ]);
    }
}