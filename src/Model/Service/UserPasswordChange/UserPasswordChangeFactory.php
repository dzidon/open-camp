<?php

namespace App\Model\Service\UserPasswordChange;

use App\Model\Entity\UserPasswordChange;
use App\Model\Library\UserPasswordChange\UserPasswordChangeResult;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Security\Hasher\UserPasswordChangeVerifierHasherInterface;
use App\Service\Security\Token\TokenSplitterInterface;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @inheritDoc
 */
class UserPasswordChangeFactory implements UserPasswordChangeFactoryInterface
{
    private int $maxActivePasswordChangesPerUser;
    private string $passwordChangeLifespan;
    private array $newSelectors = [];

    private TokenSplitterInterface $tokenSplitter;
    private UserPasswordChangeRepositoryInterface $userPasswordChangeRepository;
    private UserPasswordChangeVerifierHasherInterface $verifierHasher;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        TokenSplitterInterface                    $tokenSplitter,
        UserPasswordChangeRepositoryInterface     $userPasswordChangeRepository,
        UserPasswordChangeVerifierHasherInterface $verifierHasher,
        UserRepositoryInterface                   $userRepository,

        #[Autowire('%app.max_active_password_changes_per_user%')]
        int $maxActivePasswordChangesPerUser,

        #[Autowire('%app.password_change_lifespan%')]
        string $passwordChangeLifespan
    ) {
        $this->tokenSplitter = $tokenSplitter;
        $this->userPasswordChangeRepository = $userPasswordChangeRepository;
        $this->verifierHasher = $verifierHasher;
        $this->userRepository = $userRepository;

        $this->maxActivePasswordChangesPerUser = $maxActivePasswordChangesPerUser;
        $this->passwordChangeLifespan = $passwordChangeLifespan;
    }

    /**
     * @inheritDoc
     */
    public function createUserPasswordChange(string $email): UserPasswordChangeResult
    {
        $fake = false;

        // this email might not be registered
        $user = $this->userRepository->findOneByEmail($email);
        if ($user === null)
        {
            $fake = true;
        }
        else
        {
            // maximum amount of active password changes might have been reached
            $activePasswordChanges = $this->userPasswordChangeRepository->findByUser($user, true);
            if (count($activePasswordChanges) >= $this->maxActivePasswordChangesPerUser)
            {
                $fake = true;
            }
        }

        // make sure the selector is unique
        $selector = null;
        $plainVerifier = '';

        while ($selector === null || $this->userPasswordChangeRepository->selectorExists($selector) || in_array($selector, $this->newSelectors))
        {
            $tokenSplit = $this->tokenSplitter->generateTokenSplit();
            $selector = $tokenSplit->getSelector();
            $plainVerifier = $tokenSplit->getPlainVerifier();
        }

        $this->newSelectors[] = $selector;

        // create a password change
        $expireAt = new DateTimeImmutable(sprintf('+%s', $this->passwordChangeLifespan));
        $verifier = $this->verifierHasher->hashVerifier($plainVerifier);
        $userPasswordChange = new UserPasswordChange($expireAt, $selector, $verifier);

        // assign user
        if ($user !== null)
        {
            $userPasswordChange->setUser($user);
        }

        return new UserPasswordChangeResult($userPasswordChange, $plainVerifier, $fake);
    }
}