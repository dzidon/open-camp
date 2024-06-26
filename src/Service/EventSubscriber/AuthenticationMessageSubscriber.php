<?php

namespace App\Service\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Informs the user about logging in and out.
 */
class AuthenticationMessageSubscriber
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    #[AsEventListener(event: LoginSuccessEvent::class, priority: 300)]
    public function onLogin(LoginSuccessEvent $event): void
    {
        $token = $event->getAuthenticatedToken();
        if ($token instanceof RememberMeToken)
        {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();
        $flashBag = $session->getFlashBag();

        $message = $this->translator->trans('auth.logged_in');
        $flashBag->add('success', $message);
    }

    #[AsEventListener(event: LogoutEvent::class)]
    public function onLogout(LogoutEvent $event): void
    {
        $token = $event->getToken();
        if ($token === null)
        {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();
        $flashBag = $session->getFlashBag();

        $message = $this->translator->trans('auth.logged_out');
        $flashBag->add('success', $message);
    }
}