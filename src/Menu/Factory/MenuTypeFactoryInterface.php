<?php

namespace App\Menu\Factory;

use App\Menu\Type\MenuTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Interface for menu factories that should be added to the menu factory registry.
 */
interface MenuTypeFactoryInterface
{
    /**
     * Returns a unique menu identifier. The menu will be available in the registry
     * under this identifier.
     *
     * @return string
     */
    public static function getMenuIdentifier(): string;

    /**
     * Instantiates a menu.
     *
     * @param array $options
     * @return MenuTypeInterface
     */
    public function buildMenuType(array $options = []): MenuTypeInterface;

    /**
     * Configures options for the factory.
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void;
}