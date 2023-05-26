<?php

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Tells the user that they need to log in to access a protected resource.
 */
class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private Security $security;
    private TranslatorInterface $translator;

    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security, TranslatorInterface $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        $session = $request->getSession();
        $flashBag = $session->getFlashBag();

        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            $message = $this->translator->trans('auth.login_repeat_required');
        }
        else
        {
            $message = $this->translator->trans('auth.login_required');
        }

        $flashBag->add('warning', $message);
        $url = $this->urlGenerator->generate('user_login');

        return new RedirectResponse($url);
    }
}