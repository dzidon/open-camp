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

    private bool $isPurchasableItemsIndividualMode;

    private string $simpleIdCharacters;

    private int $simpleIdLength;

    private array $newSimpleIds = [];

    private int $highestInvoiceNumber = 0;

    public function __construct(ApplicationRepositoryInterface $applicationRepository,
                                bool                           $isPurchasableItemsIndividualMode,
                                string                         $simpleIdCharacters,
                                int                            $simpleIdLength)
    {
        $this->applicationRepository = $applicationRepository;
        $this->isPurchasableItemsIndividualMode = $isPurchasableItemsIndividualMode;
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

        $billingData = $data->getBillingData();
        $nameFirst = $billingData->getNameFirst();
        $nameLast = $billingData->getNameLast();
        $street = $billingData->getStreet();
        $town = $billingData->getTown();
        $zip = $billingData->getZip();
        $country = $billingData->getCountry();

        $isEuBusinessDataEnabled = $data->isEuBusinessDataEnabled();
        $isNationalIdentifierEnabled = $data->isNationalIdentifierEnabled();
        $currency = $data->getCurrency();
        $tax = $data->getTax();
        $discountConfig = $campDate->getDiscountConfig();
        $contactsData = $data->getContactsData();
        $isEmailMandatory = false;
        $isPhoneNumberMandatory = false;

        // simple id

        while ($simpleId === null || $this->applicationRepository->simpleIdExists($simpleId) || in_array($simpleId, $this->newSimpleIds))
        {
            $simpleId = $this->generateRandomSimpleId();
        }

        $this->newSimpleIds[] = $simpleId;

        // invoice number

        $highestInvoiceNumberFromDb = (int) $this->applicationRepository->getHighestInvoiceNumber();

        if ($highestInvoiceNumberFromDb > $this->highestInvoiceNumber)
        {
            $this->highestInvoiceNumber = $highestInvoiceNumberFromDb;
        }

        $this->highestInvoiceNumber++;
        $invoiceNumber = $this->highestInvoiceNumber;

        // are email & phone number mandatory

        if (!empty($contactsData))
        {
            $firstKey = array_key_first($contactsData);
            $referenceContactData = $contactsData[$firstKey];
            $isEmailMandatory = $referenceContactData->isEmailMandatory();
            $isPhoneNumberMandatory = $referenceContactData->isPhoneNumberMandatory();
        }

        // discount config

        $discountRecurringCampersConfig = [];
        $discountSiblingsConfig = [];

        if ($discountConfig !== null)
        {
            $discountRecurringCampersConfig = $discountConfig->getRecurringCampersConfig();
            $discountSiblingsConfig = $discountConfig->getSiblingsConfig();
        }

        // instantiate

        $application = new Application(
            $simpleId,
            $invoiceNumber,
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
            $this->isPurchasableItemsIndividualMode,
            $campDate
        );

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