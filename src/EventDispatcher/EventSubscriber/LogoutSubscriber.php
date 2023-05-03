<?php

namespace App\EventDispatcher\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Informs the user about logging out.
 */
class LogoutSubscriber
{
    public TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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