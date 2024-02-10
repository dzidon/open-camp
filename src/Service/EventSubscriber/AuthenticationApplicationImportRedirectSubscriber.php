<?php

namespace App\Service\EventSubscriber;

use App\Model\Entity\User;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationToUserImporterInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class AuthenticationApplicationImportRedirectSubscriber
{
    private UrlGeneratorInterface $urlGenerator;

    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationToUserImporterInterface $applicationToUserImporter;

    public function __construct(UrlGeneratorInterface              $urlGenerator,
                                ApplicationRepositoryInterface     $applicationRepository,
                                ApplicationToUserImporterInterface $applicationToUserImporter)
    {
        $this->urlGenerator = $urlGenerator;
        $this->applicationRepository = $applicationRepository;
        $this->applicationToUserImporter = $applicationToUserImporter;
    }

    #[AsEventListener(event: LoginSuccessEvent::class, priority: 100)]
    public function onLogin(LoginSuccessEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();
        $request = $event->getRequest();
        $session = $request->getSession();
        $targetPath = $session->get('_security.main.target_path');

        if ($targetPath !== null)
        {
            return;
        }

        $application = $this->applicationRepository->findLastCompletedFromSession();

        if ($application === null)
        {
            return;
        }

        if (!$this->applicationToUserImporter->canImportApplicationToUser($application, $user))
        {
            return;
        }

        $redirectUrl = $this->urlGenerator->generate('user_application_import');
        $redirectResponse = new RedirectResponse($redirectUrl);
        $event->setResponse($redirectResponse);
    }
}