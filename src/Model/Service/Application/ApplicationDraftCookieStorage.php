<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\UuidV4;

/**
 * Stores user's application draft ids in cookies.
 */
class ApplicationDraftCookieStorage implements ApplicationDraftHttpStorageInterface
{
    private string $cookieKeyPrefix;

    private string $cookieLifespan;

    private RequestStack $requestStack;

    public function __construct(
        RequestStack $requestStack,

        #[Autowire('%app.cookie_prefix_application_draft%')]
        string $cookieKeyPrefix,

        #[Autowire('%app.cookie_lifespan_application_draft%')]
        string $cookieLifespan
    ) {
        $this->requestStack = $requestStack;
        $this->cookieKeyPrefix = $cookieKeyPrefix;
        $this->cookieLifespan = $cookieLifespan;
    }

    /**
     * @inheritDoc
     */
    public function storeApplicationDraft(Application $application, Response $response): void
    {
        if (!$application->isDraft())
        {
            return;
        }

        $campDate = $application->getCampDate();

        if ($campDate === null)
        {
            return;
        }

        $applicationIdString = $this->getApplicationIdString($application);
        $cookieKey = $this->getCookieKey($campDate);
        $this->setCookie($cookieKey, $applicationIdString, $response);
    }

    /**
     * @inheritDoc
     */
    public function getApplicationDraftId(CampDate $campDate): ?UuidV4
    {
        $request = $this->getCurrentRequest();
        $cookieKey = $this->getCookieKey($campDate);

        if (!$request->cookies->has($cookieKey))
        {
            return null;
        }

        $applicationDraftId = $request->cookies->get($cookieKey);

        if (!UuidV4::isValid($applicationDraftId))
        {
            return null;
        }

        return UuidV4::fromString($applicationDraftId);
    }

    /**
     * @inheritDoc
     */
    public function getApplicationDraftIds(): array
    {
        $request = $this->getCurrentRequest();
        $cookies = $request->cookies->all();
        $applicationIds = [];

        foreach ($cookies as $cookieKey => $cookieValue)
        {
            if (!$this->cookieKeyStartsWithPrefix($cookieKey))
            {
                continue;
            }

            if (!UuidV4::isValid($cookieValue))
            {
                continue;
            }

            $applicationIds[] = UuidV4::fromString($cookieValue);
        }

        return $applicationIds;
    }

    /**
     * @inheritDoc
     */
    public function removeApplicationDraft(Application|CampDate $target, Response $response): void
    {
        $campDate = $target;

        if ($target instanceof Application)
        {
            $campDate = $target->getCampDate();
        }

        if ($campDate === null)
        {
            return;
        }

        $cookieKey = $this->getCookieKey($campDate);
        $this->removeCookie($cookieKey, $response);
    }

    private function getCurrentRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    private function getCookieKey(CampDate $campDate): string
    {
        $campDateIdString = $this->getCampDateIdString($campDate);

        return sprintf('%s%s', $this->cookieKeyPrefix, $campDateIdString);
    }

    private function cookieKeyStartsWithPrefix(string $cookieKey): bool
    {
        return str_starts_with($cookieKey, $this->cookieKeyPrefix);
    }

    private function setCookie(string $cookieKey, string $applicationIdString, Response $response): void
    {
        $offset = sprintf('+%s', $this->cookieLifespan);
        $cookie = new Cookie($cookieKey, $applicationIdString, new DateTimeImmutable($offset));
        $response->headers->setCookie($cookie);
    }

    private function removeCookie(string $cookieKey, Response $response): void
    {
        $expiration = time() - 3600;
        $cookie = new Cookie($cookieKey, '', $expiration);
        $response->headers->setCookie($cookie);
    }

    private function getCampDateIdString(CampDate $campDate): string
    {
        return $campDate
            ->getId()
            ->toRfc4122()
        ;
    }

    private function getApplicationIdString(Application $application): string
    {
        return $application
            ->getId()
            ->toRfc4122()
        ;
    }
}