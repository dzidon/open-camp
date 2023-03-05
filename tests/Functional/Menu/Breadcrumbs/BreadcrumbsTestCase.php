<?php

namespace App\Tests\Functional\Menu\Breadcrumbs;

use App\Menu\Registry\MenuTypeRegistryInterface;
use App\Menu\Type\MenuTypeInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abstraction for all breadcrumbs tests.
 */
abstract class BreadcrumbsTestCase extends KernelTestCase
{
    protected ContainerInterface $container;
    protected MenuTypeRegistryInterface $menuTypeRegistry;

    /**
     * Returns an array of identifiers of all child menu types.
     *
     * @param MenuTypeInterface $breadcrumbsMenu
     * @return array
     */
    protected function getChildrenIdentifiers(MenuTypeInterface $breadcrumbsMenu): array
    {
        $identifiers = [];
        foreach ($breadcrumbsMenu->getChildren() as $breadcrumb)
        {
            $identifiers[] = $breadcrumb->getIdentifier();
        }

        return $identifiers;
    }

    /**
     * Boots the kernel and stores the service container and the menu type registry to attributes for later use.
     *
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();

        /** @var MenuTypeRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeRegistryInterface::class);
        $this->menuTypeRegistry = $menuTypeRegistry;
    }
}