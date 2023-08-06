<?php

namespace App\Library\Data\User;

/**
 * Profile billing information data.
 */
interface BillingDataInterface
{
    public function isEuBusinessDataEnabled(): bool;

    public function getNameFirst(): ?string;

    public function setNameFirst(?string $nameFirst): self;

    public function getNameLast(): ?string;

    public function setNameLast(?string $nameLast): self;

    public function getStreet(): ?string;

    public function setStreet(?string $street): self;

    public function getTown(): ?string;

    public function setTown(?string $town): self;

    public function getZip(): ?string;

    public function setZip(?string $zip): self;

    public function getCountry(): ?string;

    public function setCountry(?string $country): self;

    public function isCompany(): bool;

    public function setIsCompany(bool $isCompany): self;

    public function getBusinessName(): ?string;

    public function setBusinessName(?string $businessName): self;

    public function getBusinessCin(): ?string;

    public function setBusinessCin(?string $businessCin): self;

    public function getBusinessVatId(): ?string;

    public function setBusinessVatId(?string $businessVatId): self;
}