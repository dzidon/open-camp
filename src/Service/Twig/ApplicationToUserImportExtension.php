<?php

namespace App\Service\Twig;

use App\Model\Entity\Application;
use App\Model\Service\Application\ApplicationToUserImporterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\UuidV4;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds the ability to find out if the given application can be imported to the given user.
 */
class ApplicationToUserImportExtension extends AbstractExtension
{
    private ApplicationToUserImporterInterface $applicationToUserImporter;

    private RequestStack $requestStack;

    private string $lastCompletedApplicationIdSessionKey;

    public function __construct(ApplicationToUserImporterInterface $applicationToUserImporter,
                                RequestStack                       $requestStack,
                                string                             $lastCompletedApplicationIdSessionKey)
    {
        $this->applicationToUserImporter = $applicationToUserImporter;
        $this->requestStack = $requestStack;
        $this->lastCompletedApplicationIdSessionKey = $lastCompletedApplicationIdSessionKey;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('can_import_application_to_user', [$this->applicationToUserImporter, 'canImportApplicationToUser']),
            new TwigFunction('is_application_stored_in_session', [$this, 'isApplicationStoredInSession']),
        ];
    }

    public function isApplicationStoredInSession(Application $application): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $lastCompletedApplicationIdString = $session->get($this->lastCompletedApplicationIdSessionKey);

        if ($lastCompletedApplicationIdString === null)
        {
            return false;
        }

        if (!UuidV4::isValid($lastCompletedApplicationIdString))
        {
            return false;
        }

        $applicationIdString = $application
            ->getId()
            ->toRfc4122()
        ;

        return $applicationIdString === $lastCompletedApplicationIdString;
    }
}