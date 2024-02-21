<?php

namespace App\Service\Menu\Breadcrumbs\Admin\User;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\User;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_user_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_user_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var User $user */
        $user = $options['user'];

        $this->addRoute($breadcrumbs, 'admin_user_read', [
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