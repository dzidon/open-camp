<?php

namespace App\Model\Service\Application;

use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Repository\ApplicationRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @inheritDoc
 */
class ApplicationFactory implements ApplicationFactoryInterface
{
    private ApplicationRepositoryInterface $applicationRepository;

    private Security $security;

    private string $simpleIdCharacters;

    private int $simpleIdLength;

    private array $newSimpleIds = [];

    public function __construct(ApplicationRepositoryInterface $applicationRepository,
                                Security                       $security,
                                string                         $simpleIdCharacters,
                                int                            $simpleIdLength)
    {
        $this->applicationRepository = $applicationRepository;
        $this->security = $security;
        $this->simpleIdCharacters = $simpleIdCharacters;
        $this->simpleIdLength = $simpleIdLength;
    }

    /**
     * @inheritDoc
     */
    public function createApplication(ApplicationStepOneData $data, CampDate $campDate, ?User $user = null): Application
    {
        $simpleId = null;
        $email = $data->getEmail();
        $nameFirst = $data->getNameFirst();
        $nameLast = $data->getNameLast();
        $street = $data->getStreet();
        $town = $data->getTown();
        $zip = $data->getZip();
        $country = $data->getCountry();
        $isEuBusinessDataEnabled = $data->isEuBusinessDataEnabled();
        $isNationalIdentifierEnabled = $data->isNationalIdentifierEnabled();
        $currency = $data->getCurrency();
        $tax = $data->getTax();
        $discountConfig = $campDate->getDiscountConfig();
        $contactsData = $data->getContactsData();
        $campDateDescription = $campDate->getDescription();
        $isEmailMandatory = false;
        $isPhoneNumberMandatory = false;

        while ($simpleId === null || $this->applicationRepository->simpleIdExists($simpleId) || in_array($simpleId, $this->newSimpleIds))
        {
            $simpleId = $this->generateRandomSimpleId();
        }

        $this->newSimpleIds[] = $simpleId;

        if (!empty($contactsData))
        {
            $firstKey = array_key_first($contactsData);
            $referenceContactData = $contactsData[$firstKey];
            $isEmailMandatory = $referenceContactData->isEmailMandatory();
            $isPhoneNumberMandatory = $referenceContactData->isPhoneNumberMandatory();
        }

        $discountRecurringCampersConfig = [];
        $discountSiblingsConfig = [];

        if ($discountConfig !== null)
        {
            $discountRecurringCampersConfig = $discountConfig->getRecurringCampersConfig();
            $discountSiblingsConfig = $discountConfig->getSiblingsConfig();
        }

        $application = new Application(
            $simpleId,
            $email,
            $nameFirst,
            $nameLast,
            $street,
            $town,
            $zip,
            $country,
            $currency,
            $tax,
            $discountRecurringCampersConfig,
            $discountSiblingsConfig,
            $isEuBusinessDataEnabled,
            $isNationalIdentifierEnabled,
            $isEmailMandatory,
            $isPhoneNumberMandatory,
            $campDate,
            $campDateDescription
        );

        /** @var null|User $user */
        $user = $this->security->getUser();
        $application->setUser($user);

        return $application;
    }

    private function generateRandomSimpleId(): string
    {
        $simpleId = '';

        for ($i = 0; $i < $this->simpleIdLength; $i++)
        {
            $index = rand(0, mb_strlen($this->simpleIdCharacters, 'utf-8') - 1);
            $simpleId .= $this->simpleIdCharacters[$index];
        }

        return $simpleId;
    }
}