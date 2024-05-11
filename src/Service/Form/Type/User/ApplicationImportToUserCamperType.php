<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationImportToUserCamperData;
use App\Model\Entity\Camper;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\UuidV4;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicationImportToUserCamperType extends AbstractType
{
    private TranslatorInterface $translator;

    private string $dateFormat;

    public function __construct(
        TranslatorInterface $translator,

        #[Autowire('%app.date_format%')]
        string $dateFormat
    ) {
        $this->translator = $translator;
        $this->dateFormat = $dateFormat;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void
        {
            /** @var null|ApplicationImportToUserCamperData $applicationImportToUserCamperData */
            $applicationImportToUserCamperData = $event->getData();
            $form = $event->getForm();

            if ($applicationImportToUserCamperData === null)
            {
                return;
            }

            $importToCamperChoices = $applicationImportToUserCamperData->getImportToCamperChoices();

            $form
                ->add('importToCamper', ChoiceType::class, [
                    'label'                     => 'form.user.application_import_to_user_camper.action',
                    'expanded'                  => true,
                    'choice_translation_domain' => false,
                    'choices'                   => $importToCamperChoices,
                    'choice_value'              => function (null|bool|Camper $camper): string|UuidV4
                    {
                        if ($camper === null)
                        {
                            return '';
                        }
                        else if ($camper === true)
                        {
                            return 'true';
                        }
                        else if ($camper === false)
                        {
                            return 'false';
                        }

                        return $camper->getId();
                    },
                    'choice_label' => function (bool|Camper $camper): string
                    {
                        if ($camper === true)
                        {
                            return $this->translator->trans('form.user.application_import_to_user_camper.create_new');
                        }

                        if ($camper === false)
                        {
                            return $this->translator->trans('form.user.application_import_to_user_camper.do_nothing');
                        }

                        $bornAtLabel = $this->translator->trans('form.user.application_import_to_user_camper.born_at');
                        $bornAtString = $camper
                            ->getBornAt()
                            ->format($this->dateFormat)
                        ;

                        $camperLabel = sprintf('%s (%s %s)', $camper->getNameFull(), $bornAtLabel, $bornAtString);

                        return $this->translator->trans('form.user.application_import_to_user_camper.update_existing', [
                            'camper' => $camperLabel
                        ]);
                    },
                ])
            ;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => ApplicationImportToUserCamperData::class,
            'block_prefix' => 'user_application_import_camper',
            'label'        => false,
        ]);
    }
}