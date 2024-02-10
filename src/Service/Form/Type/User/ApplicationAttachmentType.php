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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * User application attachment edit.
 */
class ApplicationAttachmentType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

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
            $helpText = $applicationAttachmentData->getHelp();
            $helpTextParts = [];

            if ($isAlreadyUploaded)
            {
                $alreadyUploadedMessage = $this->translator->trans('form.user.application_attachment.already_uploaded');
                $helpTextParts[] = "<strong class=\"text-success\">&check; $alreadyUploadedMessage</strong>";
            }

            if ($helpText !== null && $helpText !== '')
            {
                $helpTextParts[] = $helpText;
            }

            if ($applicationAttachmentData->getRequiredType() === AttachmentConfigRequiredTypeEnum::REQUIRED_LATER)
            {
                $helpTextParts[] = $this->translator->trans('form.user.application_attachment.is_required_later');
            }

            $extensionsText = implode(', ', $applicationAttachmentData->getExtensions());

            if (!empty($extensionsText))
            {
                $allowedFormatsText = $this->translator->trans('form.user.application_attachment.allowed_formats');
                $helpTextParts[] = sprintf('%s: %s', $allowedFormatsText, $extensionsText);
            }

            $help = implode('<br>', $helpTextParts);

            $form
                ->add('file', FileType::class, [
                    'multiple'           => false,
                    'required'           => $isRequired,
                    'label'              => $label,
                    'help'               => $help,
                    'help_html'          => true,
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
            'row_attr'   => [
                'class' => 'm-0',
            ],
        ]);
    }
}