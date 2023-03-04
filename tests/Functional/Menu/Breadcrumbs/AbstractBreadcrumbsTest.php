<?php

namespace App\Tests\Functional\Menu\Breadcrumbs;

use App\Menu\Breadcrumbs\AbstractBreadcrumbs;
use App\Menu\Registry\MenuTypeRegistry;
use App\Menu\Registry\MenuTypeRegistryInterface;
use App\Menu\Type\MenuTypeInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abstraction for all breadcrumbs tests.
 */
abstract class AbstractBreadcrumbsTest extends KernelTestCase
{
    protected ContainerInterface $container;

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
     * Boots the kernel and stores the service container to an attribute for later use.
     *
     * @return void
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();
    }

    /**
     * Returns an instance of a specified breadcrumbs service from the service container.
     *
     * @param string $breadcrumbsClass
     * @return AbstractBreadcrumbs
     */
    protected function getBreadcrumbs(string $breadcrumbsClass): AbstractBreadcrumbs
    {
        if (!is_subclass_of($breadcrumbsClass, AbstractBreadcrumbs::class))
        {
            throw new LogicException(
                sprintf('Your breadcrumbs test is trying to create an instance of "%s", but this class 
                does not extend "%s".', $breadcrumbsClass, AbstractBreadcrumbs::class)
            );
        }

        /** @var AbstractBreadcrumbs $breadcrumbs */
        $breadcrumbs = $this->container->get($breadcrumbsClass);
        return $breadcrumbs;
    }

    /**
     * Returns an instance of the central menu type registry from the service container.
     *
     * @return MenuTypeRegistry
     */
    protected function getMenuTypeRegistry(): MenuTypeRegistry
    {
        /** @var MenuTypeRegistryInterface $menuTypeRegistry */
        $menuTypeRegistry = $this->container->get(MenuTypeRegistry::class);
        return $menuTypeRegistry;
    }
}