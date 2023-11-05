<?php

namespace App\Tests\Model\Repository;

use App\Model\Entity\CampDateFormField;
use App\Model\Repository\CampDateFormFieldRepository;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Repository\FormFieldRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class CampDateFormFieldRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $campDateFormFieldRepository = $this->getCampDateFormFieldRepository();
        $formFieldRepository = $this->getFormFieldRepository();
        $campDateRepository = $this->getCampDateRepository();

        $campDate = $campDateRepository->findOneById(new UuidV4('550e8400-e29b-41d4-a716-446655440000'));
        $formField = $formFieldRepository->findOneById(new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $campDateFormField = new CampDateFormFIeld($campDate, $formField, 300);
        $campDateFormFieldRepository->saveCampDateFormField($campDateFormField, true);

        $loadedCampDateFormFields = $campDateFormFieldRepository->findByCampDate($campDate);
        $this->assertCount(1, $loadedCampDateFormFields);
        $loadedCampDateFormField = $loadedCampDateFormFields[0];
        $this->assertSame($formField, $loadedCampDateFormField->getFormField());
        $this->assertSame($campDate, $loadedCampDateFormField->getCampDate());

        $campDateFormFieldRepository->removeCampDateFormField($campDateFormField, true);
        $loadedCampDateFormFields = $campDateFormFieldRepository->findByCampDate($campDate);
        $this->assertCount(0, $loadedCampDateFormFields);
    }

    public function testFindByCampDate(): void
    {
        $campDateFormFieldRepository = $this->getCampDateFormFieldRepository();
        $campDateRepository = $this->getCampDateRepository();

        $campDate = $campDateRepository->findOneById(new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $loadedCampDateFormFields = $campDateFormFieldRepository->findByCampDate($campDate);
        $this->assertCount(2, $loadedCampDateFormFields);
    }

    private function getFormFieldRepository(): FormFieldRepositoryInterface
    {
        $container = static::getContainer();

        /** @var FormFieldRepositoryInterface $repository */
        $repository = $container->get(FormFieldRepositoryInterface::class);

        return $repository;
    }

    private function getCampDateRepository(): CampDateRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampDateRepositoryInterface $repository */
        $repository = $container->get(CampDateRepositoryInterface::class);

        return $repository;
    }

    private function getCampDateFormFieldRepository(): CampDateFormFieldRepository
    {
        $container = static::getContainer();

        /** @var CampDateFormFieldRepository $repository */
        $repository = $container->get(CampDateFormFieldRepository::class);

        return $repository;
    }
}