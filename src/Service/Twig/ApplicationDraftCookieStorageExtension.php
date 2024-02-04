<?php

namespace App\Service\Twig;

use App\Model\Service\Application\ApplicationDraftHttpStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds the ability to get user's application draft id for the given camp date.
 */
class ApplicationDraftCookieStorageExtension extends AbstractExtension
{
    private ApplicationDraftHttpStorageInterface $applicationDraftHttpStorage;

    public function __construct(ApplicationDraftHttpStorageInterface $applicationDraftHttpStorage)
    {
        $this->applicationDraftHttpStorage = $applicationDraftHttpStorage;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_application_draft_id', [$this->applicationDraftHttpStorage, 'getApplicationDraftId']),
        ];
    }
}