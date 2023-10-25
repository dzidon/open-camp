<?php

namespace App\Tests\Model\Repository;

use App\Library\Data\Admin\FormFieldSearchData;
use App\Library\Enum\Search\Data\Admin\FormFieldSortEnum;
use App\Model\Entity\FormField;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Repository\FormFieldRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class FormFieldRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $repository = $this->getFormFieldRepository();

        $formField = new FormField('Field', FormFieldTypeEnum::TEXT, 'Field:');
        $repository->saveFormField($formField, true);
        $id = $formField->getId();

        $loadedFormField = $repository->findOneById($id);
        $this->assertNotNull($loadedFormField);
        $this->assertSame($id, $loadedFormField->getId());

        $repository->removeFormField($formField, true);
        $loadedFormField = $repository->findOneById($id);
        $this->assertNull($loadedFormField);
    }

    public function testFindOneById(): void
    {
        $repository = $this->getFormFieldRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $formField = $repository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $formField->getId()->toRfc4122());
    }

    public function testFindOneByName(): void
    {
        $repository = $this->getFormFieldRepository();

        $formField = $repository->findOneByName('Field 1');
        $this->assertSame('Field 1', $formField->getName());
    }

    public function testGetAdminPaginator(): void
    {
        $repository = $this->getFormFieldRepository();

        $data = new FormFieldSearchData();
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getFormFieldNames($paginator->getCurrentPageItems());
        $this->assertSame(['Field 2', 'Field 1'], $names);
    }

    public function testGetAdminPaginatorWithName(): void
    {
        $repository = $this->getFormFieldRepository();

        $data = new FormFieldSearchData();
        $data->setPhrase('eld 1');

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getFormFieldNames($paginator->getCurrentPageItems());
        $this->assertSame(['Field 1'], $names);
    }

    public function testGetAdminPaginatorWithType(): void
    {
        $repository = $this->getFormFieldRepository();

        $data = new FormFieldSearchData();
        $data->setType(FormFieldTypeEnum::NUMBER);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getFormFieldNames($paginator->getCurrentPageItems());
        $this->assertSame(['Field 2'], $names);
    }

    public function testGetAdminPaginatorWithRequired(): void
    {
        $repository = $this->getFormFieldRepository();

        $data = new FormFieldSearchData();
        $data->setIsRequired(true);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getFormFieldNames($paginator->getCurrentPageItems());
        $this->assertSame(['Field 2'], $names);
    }

    public function testGetAdminPaginatorWithOptional(): void
    {
        $repository = $this->getFormFieldRepository();

        $data = new FormFieldSearchData();
        $data->setIsRequired(false);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getFormFieldNames($paginator->getCurrentPageItems());
        $this->assertSame(['Field 1'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtDesc(): void
    {
        $repository = $this->getFormFieldRepository();

        $data = new FormFieldSearchData();
        $data->setSortBy(FormFieldSortEnum::CREATED_AT_DESC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getFormFieldNames($paginator->getCurrentPageItems());
        $this->assertSame(['Field 2', 'Field 1'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtAsc(): void
    {
        $repository = $this->getFormFieldRepository();

        $data = new FormFieldSearchData();
        $data->setSortBy(FormFieldSortEnum::CREATED_AT_ASC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getFormFieldNames($paginator->getCurrentPageItems());
        $this->assertSame(['Field 1', 'Field 2'], $names);
    }

    public function testGetAdminPaginatorSortByNameDesc(): void
    {
        $repository = $this->getFormFieldRepository();

        $data = new FormFieldSearchData();
        $data->setSortBy(FormFieldSortEnum::NAME_DESC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getFormFieldNames($paginator->getCurrentPageItems());
        $this->assertSame(['Field 2', 'Field 1'], $names);
    }

    public function testGetAdminPaginatorSortByNameAsc(): void
    {
        $repository = $this->getFormFieldRepository();

        $data = new FormFieldSearchData();
        $data->setSortBy(FormFieldSortEnum::NAME_ASC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getFormFieldNames($paginator->getCurrentPageItems());
        $this->assertSame(['Field 1', 'Field 2'], $names);
    }

    private function getFormFieldNames(array $formFields): array
    {
        $names = [];

        /** @var FormField $formField */
        foreach ($formFields as $formField)
        {
            $names[] = $formField->getName();
        }

        return $names;
    }

    private function getFormFieldRepository(): FormFieldRepository
    {
        $container = static::getContainer();

        /** @var FormFieldRepository $repository */
        $repository = $container->get(FormFieldRepository::class);

        return $repository;
    }
}