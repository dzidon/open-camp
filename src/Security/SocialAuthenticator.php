<?php

namespace App\Security;

use App\Repository\UserRepositoryInterface;
use App\Security\Exception\AlreadyAuthenticatedException;
use App\Security\Exception\SocialUserNotFoundException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
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
use Symfony\Bundle\SecurityBundle\Security;

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
    private Security $security;

    public function __construct(ClientRegistry $clientRegistry,
                                UserRepositoryInterface $userRepository,
                                TranslatorInterface $translator,
                                UrlGeneratorInterface $urlGenerator,
                                Security $security,
                                array|string $services)
    {
        $this->clientRegistry = $clientRegistry;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;

        if (is_string($services))
        {
            $services = explode('|', $services);
        }

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
            new UserBadge($accessToken->getToken(), function() use ($socialEmail)
            {
                $user = $this->userRepository->findOneByEmail($socialEmail);
                if ($user === null)
                {
                    $user = $this->userRepository->createUser($socialEmail);
                    $this->userRepository->saveUser($user, true);
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
        if ($targetPath !== null)
        {
            return new RedirectResponse($targetPath);
        }

        $url = $this->urlGenerator->generate('user_home');

        return new RedirectResponse($url);
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