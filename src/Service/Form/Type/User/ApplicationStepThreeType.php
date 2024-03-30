<?php

namespace App\Service\Form\Type\User;

use App\Library\Data\User\ApplicationStepThreeData;
use App\Service\Form\Type\Common\PrivacyType;
use App\Service\Form\Type\Common\TermsOfUseType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationStepThreeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void
            {
                /** @var ApplicationStepThreeData $data */
                $data = $event->getData();
                $user = $data->getUser();

                if ($user !== null)
                {
                    return;
                }

                $form = $event->getForm();
                $form->add('captcha', EWZRecaptchaType::class, [
                    'priority' => 300,
                ]);
            }
        );

        $builder
            ->add('privacy', PrivacyType::class, [
                'priority' => 200,
            ])
            ->add('terms', TermsOfUseType::class, [
                'priority' => 100,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => ApplicationStepThreeData::class,
            'block_prefix' => 'user_application_step_three',
        ]);
    }
}