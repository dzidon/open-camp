<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\FormFieldSearchData;
use App\Library\Enum\Search\Data\Admin\FormFieldSortEnum;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use PHPUnit\Framework\TestCase;

class FormFieldSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new FormFieldSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new FormFieldSearchData();
        $this->assertSame(FormFieldSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(FormFieldSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(FormFieldSortEnum::CREATED_AT_ASC);
        $this->assertSame(FormFieldSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }

    public function testType(): void
    {
        $data = new FormFieldSearchData();
        $this->assertNull($data->getType());

        $data->setType(FormFieldTypeEnum::NUMBER);
        $this->assertSame(FormFieldTypeEnum::NUMBER, $data->getType());

        $data->setType(null);
        $this->assertNull($data->getType());
    }

    public function testIsRequired(): void
    {
        $data = new FormFieldSearchData();
        $this->assertNull($data->isRequired());

        $data->setIsRequired(true);
        $this->assertTrue($data->isRequired());

        $data->setIsRequired(false);
        $this->assertFalse($data->isRequired());
    }

    public function testIsGlobal(): void
    {
        $data = new FormFieldSearchData();
        $this->assertNull($data->isGlobal());

        $data->setIsGlobal(true);
        $this->assertTrue($data->isGlobal());

        $data->setIsGlobal(false);
        $this->assertFalse($data->isGlobal());
    }
}