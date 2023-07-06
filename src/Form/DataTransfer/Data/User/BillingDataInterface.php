<?php

namespace App\Form\DataTransfer\Data\User;

/**
 * Profile billing information data.
 */
interface BillingDataInterface
{
    public function getName(): ?string;

    public function setName(?string $name): self;

    public function getStreet(): ?string;

    public function setStreet(?string $street): self;

    public function getTown(): ?string;

    public function setTown(?string $town): self;

    public function getZip(): ?string;

    public function setZip(?string $zip): self;

    public function getCountry(): ?string;

    public function setCountry(?string $country): self;
}