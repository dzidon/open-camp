<?php

namespace App\Service\Menu\Breadcrumbs\Admin\PurchasableItemVariantValue;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_purchasable_item_variant_value_delete';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_purchasable_item_variant_update';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var PurchasableItemVariantValue $purchasableItemVariantValue */
        $purchasableItemVariantValue = $options['purchasable_item_variant_value'];

        $this->addRoute($breadcrumbs, 'admin_purchasable_item_variant_value_delete', [
            'id' => $purchasableItemVariantValue->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('purchasable_item_variant_value');
        $resolver->setAllowedTypes('purchasable_item_variant_value', PurchasableItemVariantValue::class);
        $resolver->setRequired('purchasable_item_variant_value');
    }
}