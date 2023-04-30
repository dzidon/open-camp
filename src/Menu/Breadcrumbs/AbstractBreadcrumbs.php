<?php

namespace App\Menu\Breadcrumbs;

use App\Menu\Registry\MenuTypeRegistryInterface;
use App\Menu\Type\MenuType;
use LogicException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Abstraction for classes that initialize breadcrumbs.
 */
abstract class AbstractBreadcrumbs
{
    protected UrlGeneratorInterface $urlGenerator;
    protected MenuTypeRegistryInterface $menuTypeRegistry;
    protected ParameterBagInterface $parameterBag;
    protected TranslatorInterface $translator;

    public function __construct(UrlGeneratorInterface $urlGenerator,
                                MenuTypeRegistryInterface $menuTypeRegistry,
                                ParameterBagInterface $parameterBag,
                                TranslatorInterface $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->menuTypeRegistry = $menuTypeRegistry;
        $this->parameterBag = $parameterBag;
        $this->translator = $translator;
    }

    /**
     * Creates the root breadcrumbs menu type.
     *
     * @param string $block
     * @return MenuType
     */
    protected function createRoot(string $block = 'breadcrumbs'): MenuType
    {
        return new MenuType('breadcrumbs', $block);
    }

    /**
     * Adds a link to the breadcrumbs.
     *
     * @param MenuType $root
     * @param string $routeName
     * @param string|null $variation
     * @param array $urlParameters
     * @param string $block
     * @return MenuType
     */
    protected function addChildRoute(MenuType $root,
                                     string $routeName,
                                     ?string $variation = null,
                                     array $urlParameters = [],
                                     string $block = 'breadcrumb'): MenuType
    {
        $this->assertRootIdentifier($root);

        $text = $routeName;
        $routeNames = $this->parameterBag->get('app.route_trans_keys');
        if ($routeNames !== null && array_key_exists($routeName, $routeNames))
        {
            if (is_array($routeNames[$routeName]))
            {
                if ($variation === null || !array_key_exists($variation, $routeNames[$routeName]))
                {
                    $variation = array_key_first($routeNames[$routeName]);
                }

                $text = $this->translator->trans($routeNames[$routeName][$variation]);
            }
            else
            {
                $text = $this->translator->trans($routeNames[$routeName]);
            }
        }

        $url = $this->urlGenerator->generate($routeName, $urlParameters);
        $child = new MenuType($routeName, $block, $text, $url);
        $root->addChild($child);

        return $child;
    }

    /**
     * Adds the breadcrumbs to the central menu registry.
     *
     * @param MenuType $root
     * @return void
     */
    protected function registerBreadcrumbs(MenuType $root): void
    {
        $this->assertRootIdentifier($root);
        $this->menuTypeRegistry->registerMenuType($root);
    }

    /**
     * Validates that a menu has the "breadcrumbs" identifier.
     *
     * @param MenuType $root
     * @return void
     */
    private function assertRootIdentifier(MenuType $root): void
    {
        $identifier = $root->getIdentifier();
        if ($identifier !== 'breadcrumbs')
        {
            throw new LogicException(
                sprintf('Breadcrumbs root menu type must have the "breadcrumbs" identifier. "%s" used.', $identifier)
            );
        }
    }
}