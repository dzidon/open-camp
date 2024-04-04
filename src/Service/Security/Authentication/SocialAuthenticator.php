<?php

namespace App\Service\Security\Authentication;

use App\Library\Security\Authentication\Exception\AlreadyAuthenticatedException;
use App\Library\Security\Authentication\Exception\InvalidSocialEmailException;
use App\Library\Security\Authentication\Exception\SocialUserNotFoundException;
use App\Model\Entity\User;
use App\Model\Event\User\User\UserSocialLoginCreateEvent;
use App\Model\Repository\UserRepositoryInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Authenticator for third-party providers (Facebook, Google, etc).
 */
class SocialAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait;

    private array $services;

    private ClientRegistry $clientRegistry;
    private UserRepositoryInterface $userRepository;
    private TranslatorInterface $translator;
    private UrlGeneratorInterface $urlGenerator;
    private EventDispatcherInterface $eventDispatcher;
    private Security $security;

    public function __construct(ClientRegistry           $clientRegistry,
                                UserRepositoryInterface  $userRepository,
                                TranslatorInterface      $translator,
                                UrlGeneratorInterface    $urlGenerator,
                                EventDispatcherInterface $eventDispatcher,
                                Security                 $security,
                                array                    $services)
    {
        $this->clientRegistry = $clientRegistry;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->eventDispatcher = $eventDispatcher;
        $this->security = $security;
        $this->services = $services;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): ?bool
    {
        $service = $request->get('service');

        return
            $request->attributes->get('_route') === 'user_login_oauth_check' &&
            in_array($service, $this->services)
        ;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport
    {
        if ($this->security->isGranted('IS_AUTHENTICATED_FULLY'))
        {
            throw new AlreadyAuthenticatedException();
        }

        $service = $request->get('service');
        $client = $this->clientRegistry->getClient($service);
        $accessToken = $this->fetchAccessToken($client);
        $socialUser = $client->fetchUserFromToken($accessToken);
        $socialEmail = $socialUser->getEmail();

        if ($socialEmail === null)
        {
            throw new SocialUserNotFoundException();
        }

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($socialEmail): User
            {
                $user = $this->userRepository->findOneByEmail($socialEmail);

                if ($user === null)
                {
                    if (!filter_var($socialEmail, FILTER_VALIDATE_EMAIL))
                    {
                        throw new InvalidSocialEmailException();
                    }

                    $event = new UserSocialLoginCreateEvent($socialEmail);
                    $this->eventDispatcher->dispatch($event, $event::NAME);
                    $user = $event->getUser();
                }

                return $user;
            }),
        );
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);

        if ($targetPath === null)
        {
            $url = $this->urlGenerator->generate('user_home');
            $response = new RedirectResponse($url);
        }
        else
        {
            $response = new RedirectResponse($targetPath);
        }

        $response->headers->clearCookie('REMEMBERME');

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?RedirectResponse
    {
        $session = $request->getSession();
        $flashBag = $session->getFlashBag();

        $message = $this->translator->trans('auth.social_login_failed');
        $flashBag->add('failure', $message);

        $url = $this->urlGenerator->generate('user_home');

        return new RedirectResponse($url);
    }
}