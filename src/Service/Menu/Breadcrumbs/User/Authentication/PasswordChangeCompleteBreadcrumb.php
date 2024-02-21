<?php

namespace App\Service\Menu\Breadcrumbs\User\Authentication;

use App\Library\Menu\MenuTypeInterface;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordChangeCompleteBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_password_change_complete';
    }

    public function getPreviousRoute(array $options): ?string
    {
        /** @var bool $isAuthenticated */
        $isAuthenticated = $options['is_authenticated'];

        if ($isAuthenticated)
        {
            return 'user_profile_password_change_create';
        }
        else
        {
            return 'user_password_change';
        }
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var string $token */
        $token = $options['token'];

        $this->addRoute($breadcrumbs, 'user_password_change_complete', [
            'token' => $token
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('token');
        $resolver->setAllowedTypes('token', 'string');
        $resolver->setRequired('token');

        $resolver->setDefined('is_authenticated');
        $resolver->setAllowedTypes('is_authenticated', 'bool');
        $resolver->setRequired('is_authenticated');
    }
}