<?php

namespace App\Form\Type\Admin;

use App\Entity\Permission;
use App\Form\DataTransfer\Data\Admin\RoleDataInterface;
use Doctrine\ORM\EntityRepository;
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
                'choices'       => $options['choices_permissions'],
                'query_builder' => function (EntityRepository $er) {
                    return $er
                        ->createQueryBuilder('p')
                        ->select('p, pg')
                        ->leftJoin('p.group', 'pg')
                        ->addOrderBy('pg.priority', 'ASC')
                        ->addOrderBy('p.priority', 'ASC')
                    ;
                },
                'group_by'      => function (Permission $permission) {
                    $group = $permission->getPermissionGroup();
                    $label = $group->getLabel();
                    return $this->translator->trans($label);
                },
                'multiple'      => true,
                'expanded'      => true,
                'required'      => false,
                'label'         => 'form.admin.role.permissions',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'          => RoleDataInterface::class,
            'choices_permissions' => null,
        ]);

        $resolver->setAllowedTypes('choices_permissions', ['null', 'array']);
    }
}