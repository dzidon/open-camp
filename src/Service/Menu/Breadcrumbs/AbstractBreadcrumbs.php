<?php

namespace App\Service\Menu\Breadcrumbs;

use App\Library\Menu\MenuType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Abstraction for classes that create breadcrumbs.
 */
abstract class AbstractBreadcrumbs
{
    protected UrlGeneratorInterface $urlGenerator;
    protected MenuTypeFactoryRegistryInterface $menuTypeRegistry;
    protected TranslatorInterface $translator;

    public function __construct(UrlGeneratorInterface            $urlGenerator,
                                MenuTypeFactoryRegistryInterface $menuTypeRegistry,
                                TranslatorInterface              $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->menuTypeRegistry = $menuTypeRegistry;
        $this->translator = $translator;
    }

    /**
     * Creates the root breadcrumbs menu type.
     *
     * @param string $block
     * @return MenuType
     */
    protected function createBreadcrumbs(string $block = 'breadcrumbs'): MenuType
    {
        return new MenuType('breadcrumbs', $block);
    }

    /**
     * Adds a link to the breadcrumbs.
     *
     * @param MenuType $root
     * @param string $route
     * @param array $urlParameters
     * @param string|null $identifier
     * @param string $block
     * @return MenuType
     */
    protected function addRoute(MenuType $root,
                                string   $route,
                                array    $urlParameters = [],
                                ?string  $identifier = null,
                                string   $block = 'breadcrumb'): MenuType
    {
        if ($identifier === null)
        {
            $identifier = $route;
        }

        $name = $this->translator->trans("route.$route");
        $url = $this->urlGenerator->generate($route, $urlParameters);
        $child = new MenuType($identifier, $block, $name, $url);
        $root->addChild($child);

        return $child;
    }
}