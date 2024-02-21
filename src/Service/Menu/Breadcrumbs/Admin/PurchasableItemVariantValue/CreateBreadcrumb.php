<?php

namespace App\Service\Menu\Breadcrumbs\Admin\PurchasableItemVariantValue;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\PurchasableItemVariant;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_purchasable_item_variant_value_create';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_purchasable_item_variant_update';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var PurchasableItemVariant $purchasableItemVariant */
        $purchasableItemVariant = $options['purchasable_item_variant'];

        $this->addRoute($breadcrumbs, 'admin_purchasable_item_variant_value_create', [
            'id' => $purchasableItemVariant->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('purchasable_item_variant');
        $resolver->setAllowedTypes('purchasable_item_variant', PurchasableItemVariant::class);
        $resolver->setRequired('purchasable_item_variant');
    }
}