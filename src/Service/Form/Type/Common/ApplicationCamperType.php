<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\Common\ApplicationAttachmentData;
use App\Library\Data\Common\ApplicationCamperData;
use App\Library\Data\Common\ApplicationFormFieldValueData;
use App\Model\Entity\Camper;
use NumberFormatter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\UuidV4;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * User application camper edit.
 */
class ApplicationCamperType extends AbstractType
{
    private TranslatorInterface $translator;

    private RequestStack $requestStack;

    public function __construct(TranslatorInterface $translator, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // loadable campers

        $view->vars['enable_camper_loading'] = $options['enable_camper_loading'];

        /** @var Camper[] $loadableCampers */
        $loadableCampers = $options['loadable_campers'];
        $view->vars['loadable_campers'] = [];

        foreach ($loadableCampers as $loadableCamper)
        {
            $bornAtString = $loadableCamper
                ->getBornAt()
                ->format('Y-m-d')
            ;

            $view->vars['loadable_campers'][] = [
                'nameFirst'           => $loadableCamper->getNameFirst(),
                'nameLast'            => $loadableCamper->getNameLast(),
                'nationalIdentifier'  => $loadableCamper->getNationalIdentifier(),
                'bornAt'              => $bornAtString,
                'gender'              => $loadableCamper->getGender(),
                'dietaryRestrictions' => $loadableCamper->getDietaryRestrictions(),
                'healthRestrictions'  => $loadableCamper->getHealthRestrictions(),
                'medication'          => $loadableCamper->getMedication(),
            ];
        }
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $camperData = $view->children['camperData'];
        $camperDataChildren = $camperData->children;

        if (!array_key_exists('nationalIdentifier', $camperDataChildren) ||
            !array_key_exists('isNationalIdentifierAbsent', $camperDataChildren))
        {
            return;
        }

        $nationalIdentifier = $camperDataChildren['nationalIdentifier'];
        $isNationalIdentifierAbsent = $camperDataChildren['isNationalIdentifierAbsent'];

        $name = (new UuidV4())->toRfc4122();
        $searchedClassName = 'national-id-visibility';
        $newClassName = $searchedClassName . '-' . $name;

        $nationalIdentifier->vars['row_attr']['class'] = str_replace(
            $searchedClassName,
            $newClassName,
            $nationalIdentifier->vars['row_attr']['class']
        );

        $isNationalIdentifierAbsent->vars['attr']['data-cv--checkbox-cv--content-outlet'] = str_replace(
            $searchedClassName,
            $newClassName,
            $isNationalIdentifierAbsent->vars['attr']['data-cv--checkbox-cv--content-outlet']
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('camperData', CamperType::class, [
                'label'    => false,
                'priority' => 1000,
            ])
            ->add('applicationFormFieldValuesData', CollectionType::class, [
                'entry_type'    => ApplicationFormFieldValueType::class,
                'entry_options' => [
                    'row_attr'  => [
                        'class' => 'm-0',
                    ],
                ],
                'row_attr' => [
                    'class' => 'm-0',
                ],
                'label'    => false,
                'priority' => 700,
            ])
            ->add('applicationAttachmentsData', CollectionType::class, [
                'entry_type' => ApplicationAttachmentType::class,
                'entry_options' => [
                    'row_attr'  => [
                        'class' => 'm-0',
                    ],
                ],
                'row_attr'  => [
                    'class' => 'm-0',
                ],
                'label'    => false,
                'priority' => 600,
            ])
        ;

        /** @var ApplicationCamperData $defaultData */
        $defaultData = $options['empty_data'];
        $request = $this->requestStack->getCurrentRequest();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($defaultData, $request): void
        {
            /** @var null|ApplicationCamperData $applicationCamperData */
            $applicationCamperData = $event->getData();
            $form = $event->getForm();

            if ($applicationCamperData === null)
            {
                $applicationCamperData = $this->cloneDefaultApplicationCamperData($defaultData);
                $event->setData($applicationCamperData);
            }

            $fmt = numfmt_create($request->getLocale(), NumberFormatter::CURRENCY);
            $currency = $applicationCamperData->getCurrency();

            // trip there

            $tripLocationsThere = $applicationCamperData->getTripLocationsThere();

            if (!empty($tripLocationsThere))
            {
                $choices = [];

                foreach ($tripLocationsThere as $tripLocationThere)
                {
                    $name = $tripLocationThere['name'];
                    $price = $tripLocationThere['price'];
                    $priceString = $this->translator->trans('price.free');

                    if ($price > 0.0)
                    {
                        $priceString = numfmt_format_currency($fmt, $price, $currency);
                    }

                    $label = sprintf('%s (%s)', $name, $priceString);
                    $choices[$label] = $name;
                }

                $form
                    ->add('tripLocationThere', ChoiceType::class, [
                        'placeholder'               => 'form.common.choice.choose',
                        'choices'                   => $choices,
                        'label'                     => 'form.user.application_camper.trip_location_path_there',
                        'choice_translation_domain' => false,
                        'priority'                  => 900,
                    ])
                ;
            }

            // trip back

            $tripLocationsBack = $applicationCamperData->getTripLocationsBack();

            if (!empty($tripLocationsBack))
            {
                $choices = [];

                foreach ($tripLocationsBack as $tripLocationBack)
                {
                    $name = $tripLocationBack['name'];
                    $price = $tripLocationBack['price'];
                    $priceString = $this->translator->trans('price.free');

                    if ($price > 0.0)
                    {
                        $priceString = numfmt_format_currency($fmt, $price, $currency);
                    }

                    $label = sprintf('%s (%s)', $name, $priceString);
                    $choices[$label] = $name;
                }

                $form
                    ->add('tripLocationBack', ChoiceType::class, [
                        'placeholder'               => 'form.common.choice.choose',
                        'choices'                   => $choices,
                        'label'                     => 'form.user.application_camper.trip_location_path_back',
                        'choice_translation_domain' => false,
                        'priority'                  => 800,
                    ])
                ;
            }
        });
    }

    public function getParent(): string
    {
        return CollectionItemType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'             => ApplicationCamperData::class,
            'block_prefix'           => 'common_application_camper',
            'enable_camper_loading'  => false,
            'loadable_campers'       => [],
        ]);

        $resolver->setAllowedTypes('enable_camper_loading', 'bool');
        $resolver->setAllowedTypes('loadable_campers', Camper::class . '[]');

        $resolver->setRequired('empty_data');
    }

    private function cloneDefaultApplicationCamperData(ApplicationCamperData $data): ApplicationCamperData
    {
        $camperData = $data->getCamperData();
        $isNationalIdentifierEnabled = $camperData->isNationalIdentifierEnabled();
        $currency = $data->getCurrency();
        $tripLocationsThere = $data->getTripLocationsThere();
        $tripLocationsBack = $data->getTripLocationsBack();
        $applicationCamperId = $data->getApplicationCamperId();

        $newData = new ApplicationCamperData(
            $isNationalIdentifierEnabled,
            $currency,
            $tripLocationsThere,
            $tripLocationsBack,
            $applicationCamperId
        );

        foreach ($data->getApplicationAttachmentsData() as $applicationAttachmentData)
        {
            $newApplicationAttachmentData = new ApplicationAttachmentData(
                $applicationAttachmentData->getMaxSize(),
                $applicationAttachmentData->getRequiredType(),
                $applicationAttachmentData->getExtensions(),
                $applicationAttachmentData->isAlreadyUploaded(),
                $applicationAttachmentData->getPriority(),
                $applicationAttachmentData->getLabel(),
                $applicationAttachmentData->getHelp()
            );

            $newData->addApplicationAttachmentsDatum($newApplicationAttachmentData);
        }

        foreach ($data->getApplicationFormFieldValuesData() as $applicationFormFieldValueData)
        {
            $newApplicationFormFieldValueData = new ApplicationFormFieldValueData(
                $applicationFormFieldValueData->getType(),
                $applicationFormFieldValueData->isRequired(),
                $applicationFormFieldValueData->getOptions(),
                $applicationFormFieldValueData->getPriority(),
                $applicationFormFieldValueData->getLabel(),
                $applicationFormFieldValueData->getHelp()
            );

            $newData->addApplicationFormFieldValuesDatum($newApplicationFormFieldValueData);
        }

        return $newData;
    }
}