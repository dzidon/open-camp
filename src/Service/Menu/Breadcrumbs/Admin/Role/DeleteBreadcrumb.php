<?php

namespace App\Service\Menu\Breadcrumbs\Admin\Role;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Role;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_role_delete';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_role_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var Role $role */
        $role = $options['role'];

        $this->addRoute($breadcrumbs, 'admin_role_delete', [
            'id' => $role->getId(),
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('role');
        $resolver->setAllowedTypes('role', Role::class);
        $resolver->setRequired('role');
    }
}