<?php

namespace App\Service\Theme;

use DateTimeImmutable;
use LogicException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stores user's UI theme in a cookie.
 */
class ThemeCookieStorage implements ThemeHttpStorageInterface
{
    private ThemeConfigHelperInterface $themeConfigHelper;

    private RequestStack $requestStack;

    private string $themeCookieName;

    private string $themeCookieLifespan;

    public function __construct(
        ThemeConfigHelperInterface $themeConfigHelper,
        RequestStack               $requestStack,

        #[Autowire('%app.cookie_name_theme%')]
        string $themeCookieName,

        #[Autowire('%app.cookie_lifespan_theme%')]
        string $themeCookieLifespan
    ) {
        $this->themeConfigHelper = $themeConfigHelper;
        $this->requestStack = $requestStack;
        $this->themeCookieName = $themeCookieName;
        $this->themeCookieLifespan = $themeCookieLifespan;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentTheme(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        $theme = $request->cookies->get($this->themeCookieName);

        if ($theme === null || !$this->themeConfigHelper->isValidTheme($theme))
        {
            return null;
        }

        return $theme;
    }

    /**
     * @inheritDoc
     */
    public function setTheme(string $theme, Response $response): void
    {
        if (!$this->themeConfigHelper->isValidTheme($theme))
        {
            throw new LogicException(
                sprintf('Theme "%s" passed to "%s" is not supported. Include it in "app.themes".', $theme, __METHOD__)
            );
        }

        $offset = sprintf('+%s', $this->themeCookieLifespan);
        $expiresAt = new DateTimeImmutable($offset);
        $cookie = new Cookie($this->themeCookieName, $theme, $expiresAt);
        $response->headers->setCookie($cookie);
    }
}