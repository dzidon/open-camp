<?php

namespace App\Service\EventSubscriber;

use App\Service\Theme\ThemeConfigHelperInterface;
use App\Service\Theme\ThemeHttpStorageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Sets the UI theme.
 */
class ThemeHttpStorageSubscriber
{
    private ThemeConfigHelperInterface $themeConfigHelper;

    private ThemeHttpStorageInterface $themeHttpStorage;

    private UrlGeneratorInterface $urlGenerator;

    private string $getParameterNameSetTheme;

    public function __construct(
        ThemeConfigHelperInterface $themeConfigHelper,
        ThemeHttpStorageInterface  $themeHttpStorage,
        UrlGeneratorInterface      $urlGenerator,

        #[Autowire('%app.get_param_set_theme%')]
        string $getParameterNameSetTheme
    ) {
        $this->themeConfigHelper = $themeConfigHelper;
        $this->themeHttpStorage = $themeHttpStorage;
        $this->urlGenerator = $urlGenerator;
        $this->getParameterNameSetTheme = $getParameterNameSetTheme;
    }

    #[AsEventListener(event: KernelEvents::RESPONSE)]
    public function onResponse(ResponseEvent $event): void
    {
        $currentTheme = $this->themeHttpStorage->getCurrentTheme();
        $defaultTheme = $this->themeConfigHelper->getDefaultTheme();

        if ($currentTheme === null && $defaultTheme !== null)
        {
            $response = $event->getResponse();
            $this->themeHttpStorage->setTheme($defaultTheme, $response);
        }

        $request = $event->getRequest();
        $newTheme = $request->query->get($this->getParameterNameSetTheme, '');

        if ($request->isMethod('GET') && $this->themeConfigHelper->isValidTheme($newTheme))
        {
            $route = $request->attributes->get('_route');
            $routeParams = $request->attributes->get('_route_params', []);
            $queryParams = $request->query->all();
            $params = array_merge($routeParams, $queryParams);

            if (array_key_exists($this->getParameterNameSetTheme, $params))
            {
                unset($params[$this->getParameterNameSetTheme]);
            }

            $url = $this->urlGenerator->generate($route, $params);

            $response = new RedirectResponse($url);
            $this->themeHttpStorage->setTheme($newTheme, $response);
            $event->setResponse($response);
        }
    }
}