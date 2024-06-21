<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\Common\ApplicationPurchasableItemVariantData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicationPurchasableItemVariantType extends AbstractType
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
            /** @var ApplicationPurchasableItemVariantData $applicationPurchasableItemVariantData */
            $applicationPurchasableItemVariantData = $event->getData();

            if ($applicationPurchasableItemVariantData === null)
            {
                return;
            }

            $form = $event->getForm();
            $label = $applicationPurchasableItemVariantData->getLabel();
            $validValues = $applicationPurchasableItemVariantData->getValidValues();
            $choices = [];

            foreach ($validValues as $value)
            {
                $choices[$value] = $value;
            }

            $form->add('value', ChoiceType::class, [
                'choices'                   => $choices,
                'label'                     => $label,
                'required'                  => false,
                'translation_domain'        => false,
                'choice_translation_domain' => false,
                'placeholder'               => $this->translator->trans('form.common.choice.choose'),
                'row_attr'                  => [
                    'class'                                   => 'mb-3 value-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '1',
                ],
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApplicationPurchasableItemVariantData::class,
            'label'      => false,
        ]);
    }
}