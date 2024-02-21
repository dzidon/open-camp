<?php

namespace App\Service\Menu\Breadcrumbs;

use App\Library\Menu\MenuType;
use App\Library\Menu\MenuTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Helper class for building breadcrumbs.
 */
class AbstractBreadcrumb
{
    protected UrlGeneratorInterface $urlGenerator;
    protected TranslatorInterface $translator;

    public function __construct(UrlGeneratorInterface $urlGenerator,
                                TranslatorInterface   $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    /**
     * Adds a breadcrumb link to the given menu.
     *
     * @param MenuTypeInterface $breadcrumbs
     * @param string $route
     * @param array $urlParameters
     * @param string|null $identifier
     * @param string $block
     * @return MenuType
     */
    protected function addRoute(MenuTypeInterface $breadcrumbs,
                                string            $route,
                                array             $urlParameters = [],
                                ?string           $identifier = null,
                                string            $block = 'breadcrumb'): MenuType
    {
        if ($identifier === null)
        {
            $identifier = $route;
        }

        $name = $this->translator->trans("route.$route");
        $url = $this->urlGenerator->generate($route, $urlParameters);
        $child = new MenuType($identifier, $block, $name, $url);
        $breadcrumbs->addChild($child);

        return $child;
    }
}