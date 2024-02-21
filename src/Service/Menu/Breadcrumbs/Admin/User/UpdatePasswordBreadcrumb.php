<?php

namespace App\Service\Menu\Breadcrumbs\Admin\User;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\User;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdatePasswordBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_user_update_password';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_user_update';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var User $user */
        $user = $options['user'];

        $this->addRoute($breadcrumbs, 'admin_user_update_password', [
            'id' => $user->getId(),
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('user');
        $resolver->setAllowedTypes('user', User::class);
        $resolver->setRequired('user');
    }
}