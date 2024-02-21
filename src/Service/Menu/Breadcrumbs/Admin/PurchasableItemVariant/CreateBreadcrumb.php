<?php

namespace App\Service\Menu\Breadcrumbs\Admin\PurchasableItemVariant;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\PurchasableItem;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'admin_purchasable_item_variant_create';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'admin_purchasable_item_update';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var PurchasableItem $purchasableItem */
        $purchasableItem = $options['purchasable_item'];

        $this->addRoute($breadcrumbs, 'admin_purchasable_item_variant_create', [
            'id' => $purchasableItem->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('purchasable_item');
        $resolver->setAllowedTypes('purchasable_item', PurchasableItem::class);
        $resolver->setRequired('purchasable_item');
    }
}