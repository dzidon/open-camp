<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationImportToUserContactData;
use App\Model\Entity\Contact;
use App\Model\Enum\Entity\ContactRoleEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\UuidV4;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicationImportToUserContactType extends AbstractType
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
            /** @var null|ApplicationImportToUserContactData $applicationImportToUserContactData */
            $applicationImportToUserContactData = $event->getData();
            $form = $event->getForm();

            if ($applicationImportToUserContactData === null)
            {
                return;
            }

            $importToContactChoices = $applicationImportToUserContactData->getImportToContactChoices();

            $form
                ->add('importToContact', ChoiceType::class, [
                    'label'                     => 'form.user.application_import_to_user_contact.action',
                    'expanded'                  => true,
                    'choice_translation_domain' => false,
                    'choices'                   => $importToContactChoices,
                    'choice_value'              => function (null|bool|Contact $contact): string|UuidV4
                    {
                        if ($contact === null)
                        {
                            return '';
                        }
                        else if ($contact === true)
                        {
                            return 'true';
                        }
                        else if ($contact === false)
                        {
                            return 'false';
                        }

                        return $contact->getId();
                    },
                    'choice_label' => function (bool|Contact $contact): string
                    {
                        if ($contact === true)
                        {
                            return $this->translator->trans('form.user.application_import_to_user_contact.create_new');
                        }

                        if ($contact === false)
                        {
                            return $this->translator->trans('form.user.application_import_to_user_contact.do_nothing');
                        }

                        $nameFull = $contact->getNameFull();
                        $role = $contact->getRole();

                        if ($role === ContactRoleEnum::OTHER)
                        {
                            $roleString = $contact->getRoleOther();
                        }
                        else
                        {
                            $roleString = $this->translator->trans('contact_role.' . $role->value);
                        }

                        $contactLabel = sprintf('%s (%s)', $nameFull, $roleString);

                        return $this->translator->trans('form.user.application_import_to_user_contact.update_existing', [
                            'contact' => $contactLabel
                        ]);
                    },
                ])
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => ApplicationImportToUserContactData::class,
            'block_prefix' => 'user_application_import_contact',
            'label'        => false,
        ]);
    }
}