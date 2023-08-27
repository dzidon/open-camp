<?php

namespace App\Service\Form\Type\Admin;

use App\Library\Data\Admin\RoleData;
use App\Model\Entity\Permission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Admin role editing.
 */
class RoleType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'attr' => [
                    'autofocus' => 'autofocus'
                ],
                'label' => 'form.admin.role.label',
            ])
            ->add('permissions', EntityType::class, [
                'class'        => Permission::class,
                'choice_label' => function (Permission $permission) {
                    $label = $permission->getLabel();

                    return $this->translator->trans($label);
                },
                'choices'  => $options['choices_permissions'],
                'group_by' => function (Permission $permission) {
                    $group = $permission->getPermissionGroup();
                    $label = $group->getLabel();

                    return $this->translator->trans($label);
                },
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label'    => 'form.admin.role.permissions',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'          => RoleData::class,
            'choices_permissions' => [],
        ]);

        $resolver->setAllowedTypes('choices_permissions', ['array']);
    }
}