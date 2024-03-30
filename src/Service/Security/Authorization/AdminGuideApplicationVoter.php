<?php

namespace App\Service\Security\Authorization;

use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Repository\CampDateUserRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Grants admin access to camp date guides.
 */
class AdminGuideApplicationVoter extends Voter
{
    const GUIDE_ACCESS_READ = 'guide_access_read';
    const GUIDE_ACCESS_STATE = 'guide_access_state';
    const GUIDE_ACCESS_UPDATE = 'guide_access_update';
    const GUIDE_ACCESS_PAYMENTS = 'guide_access_payments';

    const GUIDE_ACCESS_PERMISSIONS = [
        self::GUIDE_ACCESS_READ,
        self::GUIDE_ACCESS_STATE,
        self::GUIDE_ACCESS_UPDATE,
        self::GUIDE_ACCESS_PAYMENTS,
    ];

    private CampDateUserRepositoryInterface $campDateUserRepository;

    private ?array $cachedCampDateUsers = null;

    public function __construct(CampDateUserRepositoryInterface $campDateUserRepository)
    {
        $this->campDateUserRepository = $campDateUserRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        $isSubjectSupported = $subject === null || $subject instanceof CampDate || $subject instanceof Application;

        if (in_array($attribute, self::GUIDE_ACCESS_PERMISSIONS) && $isSubjectSupported)
        {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User)
        {
            return false;
        }

        /** @var null|CampDate $campDate */
        $campDate = $subject;

        if ($subject instanceof Application)
        {
            $campDate = $subject->getCampDate();

            if ($campDate === null)
            {
                return false;
            }
        }

        if ($this->cachedCampDateUsers === null)
        {
            $this->cachedCampDateUsers = $this->campDateUserRepository->findByUser($user);
        }

        if ($campDate === null)
        {
            $campDateUsersToCheck = $this->cachedCampDateUsers;
        }
        else
        {
            $campDateUsersToCheck = [];
            $campDateIdString = $campDate->getId()->toRfc4122();

            foreach ($this->cachedCampDateUsers as $cachedCampDateUser)
            {
                $cachedCampDate = $cachedCampDateUser->getCampDate();
                $cachedCampDateIdString = $cachedCampDate->getId()->toRfc4122();

                if ($cachedCampDateIdString === $campDateIdString)
                {
                    $campDateUsersToCheck = [$cachedCampDateUser];

                    break;
                }
            }
        }

        foreach ($campDateUsersToCheck as $campDateUser)
        {
            if ($attribute === self::GUIDE_ACCESS_READ)
            {
                return true;
            }

            if ($attribute === self::GUIDE_ACCESS_STATE && $campDateUser->canUpdateApplicationsState())
            {
                return true;
            }

            if ($attribute === self::GUIDE_ACCESS_UPDATE && $campDateUser->canManageApplications())
            {
                return true;
            }

            if ($attribute === self::GUIDE_ACCESS_PAYMENTS && $campDateUser->canManageApplicationPayments())
            {
                return true;
            }
        }

        return false;
    }
}