<?php

namespace App\Model\Service\Application;

use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Repository\ApplicationRepositoryInterface;

/**
 * @inheritDoc
 */
class ApplicationFactory implements ApplicationFactoryInterface
{
    private ApplicationRepositoryInterface $applicationRepository;

    private string $simpleIdCharacters;

    private int $simpleIdLength;



    private array $newSimpleIds = [];

    public function __construct(ApplicationRepositoryInterface $applicationRepository,
                                string                         $simpleIdCharacters,
                                int                            $simpleIdLength)
    {
        $this->applicationRepository = $applicationRepository;
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
        $contactsData = $data->getContactsData();
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

        return new Application(
            $simpleId,
            $email,
            $nameFirst,
            $nameLast,
            $street,
            $town,
            $zip,
            $country,
            $currency,
            $isEuBusinessDataEnabled,
            $isNationalIdentifierEnabled,
            $isEmailMandatory,
            $isPhoneNumberMandatory,
            $campDate,
            $user
        );
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