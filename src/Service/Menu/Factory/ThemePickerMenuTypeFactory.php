<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use App\Service\Theme\ThemeConfigHelperInterface;
use App\Service\Theme\ThemeHttpStorageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates the theme choice menu.
 */
class ThemePickerMenuTypeFactory extends AbstractMenuTypeFactory
{
    private ThemeConfigHelperInterface $themeConfigHelper;

    private ThemeHttpStorageInterface $themeHttpStorage;

    private UrlGeneratorInterface $urlGenerator;

    private TranslatorInterface $translator;

    private RequestStack $requestStack;

    private string $getParameterNameSetTheme;

    private array $themes;

    public function __construct(
        ThemeConfigHelperInterface $themeConfigHelper,
        ThemeHttpStorageInterface  $themeProvider,
        UrlGeneratorInterface      $urlGenerator,
        TranslatorInterface        $translator,
        RequestStack               $requestStack,

        #[Autowire('%app.get_param_set_theme%')]
        string $getParameterNameSetTheme,

        #[Autowire('%app.themes%')]
        array $themes
    ) {
        $this->themeConfigHelper = $themeConfigHelper;
        $this->themeHttpStorage = $themeProvider;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->getParameterNameSetTheme = $getParameterNameSetTheme;
        $this->themes = $themes;
    }

    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'theme_picker';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        $menu = new MenuType(self::getMenuIdentifier(), 'theme_picker_root');
        $currentTheme = $this->themeHttpStorage->getCurrentTheme();

        if ($currentTheme === null)
        {
            $currentTheme = $this->themeConfigHelper->getDefaultTheme();
        }

        $request = $this->requestStack->getCurrentRequest();
        $requestParameters = $request->query;
        $requestAttributes = $request->attributes;
        $requestParametersAll = $requestParameters->all();
        $route = $requestAttributes->get('_route', '');
        $routeParams = $requestAttributes->get('_route_params', []);
        $queryParameters = array_merge($requestParametersAll, $routeParams);

        foreach ($this->themes as $theme)
        {
            try
            {
                $queryParameters[$this->getParameterNameSetTheme] = $theme;
                $url = $this->urlGenerator->generate($route, $queryParameters);
            }
            catch (RouteNotFoundException)
            {
                $url = '#';
            }

            $text = $this->translator->trans("menu_item.theme.$theme");
            $item = new MenuType($theme, 'theme_picker_item', $text, $url);
            $menu->addChild($item);

            if ($theme === $currentTheme)
            {
                $item->setActive();
            }
        }

        return $menu;
    }
}