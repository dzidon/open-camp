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
    const APPLICATION_GUIDE = 'application_guide';

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

        if ($attribute === self::APPLICATION_GUIDE && $subject instanceof Application)
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
        else if ($attribute === self::APPLICATION_GUIDE)
        {
            /** @var Application $application */
            $application = $subject;
            $campDate = $application->getCampDate();

            if ($campDate === null)
            {
                return false;
            }

            return null !== $this->campDateUserRepository->findOneForCampDateAndUser($campDate, $user);
        }

        return false;
    }
}