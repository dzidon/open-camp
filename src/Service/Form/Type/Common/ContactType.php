<?php

namespace App\Service\Form\Type\Common;

use App\Library\Data\Common\ContactData;
use App\Model\Entity\Contact;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Uid\Uuid;

/**
 * User contact edit.
 */
class ContactType extends AbstractType
{
    private PhoneNumberUtil $phoneNumberUtil;

    private string $phoneNumberFormat;

    public function __construct(
        #[Autowire('%app.phone_number_format%')]
        string $phoneNumberFormat
    ) {
        $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
        $this->phoneNumberFormat = $phoneNumberFormat;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['enable_contact_loading'] = $options['enable_contact_loading'];

        /** @var Contact[] $loadableContacts */
        $loadableContacts = $options['loadable_contacts'];
        $view->vars['loadable_contacts'] = [];

        foreach ($loadableContacts as $loadableContact)
        {
            $phoneNumber = $loadableContact->getPhoneNumber();
            $phoneNumberString = $this->phoneNumberUtil->format($phoneNumber, $this->phoneNumberFormat);

            $view->vars['loadable_contacts'][] = [
                'nameFirst'   => $loadableContact->getNameFirst(),
                'nameLast'    => $loadableContact->getNameLast(),
                'email'       => $loadableContact->getEmail(),
                'phoneNumber' => $phoneNumberString,
                'role'        => $loadableContact->getRole()->value,
                'roleOther'   => $loadableContact->getRoleOther(),
            ];
        }
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $children = $view->children;

        if (!array_key_exists('role', $children) ||
            !array_key_exists('roleOther', $children))
        {
            return;
        }

        $role = $children['role'];
        $roleOther = $children['roleOther'];

        $uid = (Uuid::v4())->toRfc4122();
        $name = $uid . '-' . $form->getName();
        $searchedClassName = 'role-other-field-visibility';
        $newClassName = $searchedClassName . '-' . $name;

        $role->vars['attr']['data-cv--other-input-cv--content-outlet'] = str_replace(
            $searchedClassName,
            $newClassName,
            $role->vars['attr']['data-cv--other-input-cv--content-outlet']
        );

        $roleOther->vars['row_attr']['class'] = str_replace(
            $searchedClassName,
            $newClassName,
            $roleOther->vars['row_attr']['class']
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var callable $emptyData */
        $emptyData = $options['empty_data'];

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($emptyData): void
        {
            /** @var null|ContactData $data */
            $data = $event->getData();

            if ($data === null)
            {
                $data = $emptyData();
                $event->setData($data);
            }

            $form = $event->getForm();
            $isEmailMandatory = $data->isEmailMandatory();
            $isPhoneNumberMandatory = $data->isPhoneNumberMandatory();

            $emailAndPhoneLabelAttr = !$isEmailMandatory && !$isPhoneNumberMandatory
                ? ['class' => 'required-conditional']
                : []
            ;

            $form
                ->add('email', EmailType::class, [
                    'required'   => $isEmailMandatory,
                    'label'      => 'form.common.contact.email',
                    'label_attr' => $emailAndPhoneLabelAttr,
                    'priority'   => 400,
                ])
                ->add('phoneNumber', PhoneNumberType::class, [
                    'required'   => $isPhoneNumberMandatory,
                    'label'      => 'form.common.contact.phone_number',
                    'label_attr' => $emailAndPhoneLabelAttr,
                    'priority'   => 300,
                ])
            ;
        });

        $builder
            ->add('nameFirst', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label'    => 'form.common.contact.name_first',
                'priority' => 600,
            ])
            ->add('nameLast', TextType::class, [
                'label'    => 'form.common.contact.name_last',
                'priority' => 500,
            ])
            ->add('role', ContactRoleType::class, [
                'placeholder'      => 'form.common.choice.choose',
                'placeholder_attr' => [
                    'disabled' => 'disabled'
                ],
                'attr' => [
                    'data-controller'                         => 'cv--other-input',
                    'data-action'                             => 'cv--other-input#updateVisibility',
                    'data-cv--other-input-cv--content-outlet' => '.role-other-field-visibility',
                ],
                'label'    => 'form.common.contact.role',
                'priority' => 200,
            ])
            ->add('roleOther', TextType::class, [
                'required'   => false,
                'label'      => 'form.common.contact.role_other',
                'label_attr' => [
                    'class' => 'required'
                ],
                'row_attr' => [
                    'class'                                   => 'mb-3 role-other-field-visibility',
                    'data-controller'                         => 'cv--content',
                    'data-cv--content-show-when-chosen-value' => '1',
                ],
                'priority' => 100,
            ])
        ;
    }

    public function getParent(): string
    {
        return CollectionItemType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'             => ContactData::class,
            'block_prefix'           => 'common_contact',
            'enable_contact_loading' => false,
            'loadable_contacts'      => [],
        ]);

        $resolver->setAllowedTypes('enable_contact_loading', 'bool');
        $resolver->setAllowedTypes('loadable_contacts', Contact::class . '[]');

        $resolver->setRequired('empty_data');
    }
}