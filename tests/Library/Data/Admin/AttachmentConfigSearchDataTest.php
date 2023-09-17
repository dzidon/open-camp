<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\AttachmentConfigSearchData;
use App\Library\Enum\Search\Data\Admin\AttachmentConfigSortEnum;
use App\Model\Entity\FileExtension;
use App\Model\Enum\Entity\AttachmentConfigRequiredTypeEnum;
use PHPUnit\Framework\TestCase;

class AttachmentConfigSearchDataTest extends TestCase
{
    public function testPhrase(): void
    {
        $data = new AttachmentConfigSearchData();
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase(null);
        $this->assertSame('', $data->getPhrase());

        $data->setPhrase('text');
        $this->assertSame('text', $data->getPhrase());
    }

    public function testSortBy(): void
    {
        $data = new AttachmentConfigSearchData();
        $this->assertSame(AttachmentConfigSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(null);
        $this->assertSame(AttachmentConfigSortEnum::CREATED_AT_DESC, $data->getSortBy());

        $data->setSortBy(AttachmentConfigSortEnum::CREATED_AT_ASC);
        $this->assertSame(AttachmentConfigSortEnum::CREATED_AT_ASC, $data->getSortBy());
    }

    public function testRequiredType(): void
    {
        $data = new AttachmentConfigSearchData();
        $this->assertNull($data->getRequiredType());

        $data->setRequiredType(AttachmentConfigRequiredTypeEnum::REQUIRED);
        $this->assertSame(AttachmentConfigRequiredTypeEnum::REQUIRED, $data->getRequiredType());

        $data->setRequiredType(null);
        $this->assertNull($data->getRequiredType());
    }

    public function testFileExtensions(): void
    {
        $data = new AttachmentConfigSearchData();
        $this->assertEmpty($data->getFileExtensions());

        $fileExtension = new FileExtension('png');
        $data->addFileExtension($fileExtension);
        $this->assertContains($fileExtension, $data->getFileExtensions());

        $data->removeFileExtension($fileExtension);
        $this->assertNotContains($fileExtension, $data->getFileExtensions());
    }
}