<?php

namespace App\Service\Translation;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Instantiates RedirectResponse objects that have to do with user's locale.
 */
interface LocaleRedirectResponseFactoryInterface
{
    /**
     * Creates an instance of RedirectResponse that can be used to redirect the user to a localized route.
     *
     * @param Request $request
     * @param string $route
     * @return RedirectResponse
     */
    public function createRedirectResponse(Request $request, string $route): RedirectResponse;
}