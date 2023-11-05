<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\FormFieldSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\FormField;
use Symfony\Component\Uid\UuidV4;

interface FormFieldRepositoryInterface
{
    /**
     * Saves a form field.
     *
     * @param FormField $formField
     * @param bool $flush
     * @return void
     */
    public function saveFormField(FormField $formField, bool $flush): void;

    /**
     * Removes a form field.
     *
     * @param FormField $formField
     * @param bool $flush
     * @return void
     */
    public function removeFormField(FormField $formField, bool $flush): void;

    /**
     * Finds all available form fields.
     *
     * @return FormField[]
     */
    public function findAll(): array;

    /**
     * Finds one form field by id.
     *
     * @param UuidV4 $id
     * @return FormField|null
     */
    public function findOneById(UuidV4 $id): ?FormField;

    /**
     * Finds one form field by name.
     *
     * @param string $name
     * @return FormField|null
     */
    public function findOneByName(string $name): ?FormField;

    /**
     * Returns admin form field search paginator.
     *
     * @param FormFieldSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(FormFieldSearchData $data, int $currentPage, int $pageSize): PaginatorInterface;
}