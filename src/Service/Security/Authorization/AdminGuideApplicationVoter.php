<?php

namespace App\Service\Security\Authorization;

use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Repository\CampDateUserRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Grants admin application access to guides.
 */
class AdminGuideApplicationVoter extends Voter
{
    const CAMP_DATE_GUIDE = 'camp_date_guide';
    const APPLICATION_GUIDE_READ = 'application_guide_read';
    const APPLICATION_GUIDE_STATE = 'application_guide_state';
    const APPLICATION_GUIDE_UPDATE = 'application_guide_update';
    const APPLICATION_GUIDE_PAYMENTS = 'application_guide_payments';

    const APPLICATION_GUIDE_PERMISSIONS = [
        self::APPLICATION_GUIDE_READ,
        self::APPLICATION_GUIDE_STATE,
        self::APPLICATION_GUIDE_UPDATE,
        self::APPLICATION_GUIDE_PAYMENTS,
    ];

    private CampDateUserRepositoryInterface $campDateUserRepository;

    public function __construct(CampDateUserRepositoryInterface $campDateUserRepository)
    {
        $this->campDateUserRepository = $campDateUserRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::CAMP_DATE_GUIDE && ($subject === null || $subject instanceof CampDate))
        {
            return true;
        }

        if (in_array($attribute, self::APPLICATION_GUIDE_PERMISSIONS) && $subject instanceof Application)
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

        if ($attribute === self::CAMP_DATE_GUIDE)
        {
            if ($subject === null)
            {
                return !empty($this->campDateUserRepository->findByUser($user));
            }
            else if ($subject instanceof CampDate)
            {
                $campDate = $subject;

                return null !== $this->campDateUserRepository->findOneForCampDateAndUser($campDate, $user);
            }
        }
        else if (in_array($attribute, self::APPLICATION_GUIDE_PERMISSIONS))
        {
            /** @var Application $application */
            $application = $subject;
            $campDate = $application->getCampDate();

            if ($campDate === null)
            {
                return false;
            }

            $campDateUser = $this->campDateUserRepository->findOneForCampDateAndUser($campDate, $user);

            if ($campDateUser === null)
            {
                return false;
            }

            if ($attribute === self::APPLICATION_GUIDE_READ)
            {
                return true;
            }

            if ($attribute === self::APPLICATION_GUIDE_STATE && $campDateUser->canUpdateApplicationsState())
            {
                return true;
            }

            if ($attribute === self::APPLICATION_GUIDE_UPDATE && $campDateUser->canUpdateApplicationsState())
            {
                return true;
            }

            if ($attribute === self::APPLICATION_GUIDE_PAYMENTS && $campDateUser->canManageApplicationPayments())
            {
                return true;
            }
        }

        return false;
    }
}