<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Library\Data\User\ApplicationStepTwoUpdateData;
use App\Model\Entity\PaymentMethod;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicationStepTwoType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('paymentMethod', EntityType::class, [
                'class'        => PaymentMethod::class,
                'choice_label' => function (PaymentMethod $paymentMethod): string
                {
                    $identifier = $paymentMethod->getIdentifier();

                    return $this->translator->trans("payment_method.$identifier");
                },
                'choices'          => $options['choices_payment_methods'],
                'label'            => 'form.user.application_step_two.payment_method',
                'placeholder'      => 'form.common.choice.choose',
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
            ])
            ->add('applicationPurchasableItemsData', CollectionType::class, [
                'entry_type'    => ApplicationPurchasableItemType::class,
                'entry_options' => [
                    'instance_defaults_data' => $options['instance_defaults_data'],
                ],
                'label' => 'form.user.application_step_two.purchasable_items',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'              => ApplicationStepTwoUpdateData::class,
            'block_prefix'            => 'user_application_step_two',
            'choices_payment_methods' => [],
        ]);

        $resolver->setAllowedTypes('choices_payment_methods', ['array']);

        $resolver->setDefined('instance_defaults_data');
        $resolver->setAllowedTypes('instance_defaults_data', ApplicationPurchasableItemInstanceData::class . '[]');
        $resolver->setRequired('instance_defaults_data');
    }
}