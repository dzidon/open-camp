<?php

namespace App\Service\Menu\Breadcrumbs\Admin\DiscountConfig;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\DiscountConfig;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReadBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_discount_config_read';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_discount_config_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var DiscountConfig $discountConfig */
        $discountConfig = $options['discount_config'];

        $this->addRoute($breadcrumbs, 'admin_discount_config_read', [
            'id' => $discountConfig->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('discount_config');
        $resolver->setAllowedTypes('discount_config', DiscountConfig::class);
        $resolver->setRequired('discount_config');
    }
}