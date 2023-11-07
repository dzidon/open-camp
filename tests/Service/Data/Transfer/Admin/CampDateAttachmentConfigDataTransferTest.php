<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateAttachmentConfig;
use App\Service\Data\Transfer\Admin\CampDateAttachmentConfigDataTransfer;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CampDateAttachmentConfigDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getCampDateAttachmentConfigDataTransfer();

        $expectedPriority = 100;
        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $campDate = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 1000.0, 10, $camp);
        $attachmentConfig = new AttachmentConfig('Config', 'Label', 10.0);
        $campDateAttachmentConfig = new CampDateAttachmentConfig($campDate, $attachmentConfig, $expectedPriority);

        $data = new CampDateAttachmentConfigData();
        $dataTransfer->fillData($data, $campDateAttachmentConfig);

        $this->assertSame($attachmentConfig, $data->getAttachmentConfig());
        $this->assertSame($expectedPriority, $data->getPriority());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCampDateAttachmentConfigDataTransfer();

        $expectedPriority = 100;
        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $campDate = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 1000.0, 10, $camp);
        $attachmentConfig = new AttachmentConfig('Config', 'Label', 10.0);
        $campDateAttachmentConfig = new CampDateAttachmentConfig($campDate, $attachmentConfig, $expectedPriority);

        $data = new CampDateAttachmentConfigData();
        $data->setPriority($expectedPriority);
        $data->setAttachmentConfig($attachmentConfig);
        $dataTransfer->fillEntity($data, $campDateAttachmentConfig);

        $this->assertSame($attachmentConfig, $campDateAttachmentConfig->getAttachmentConfig());
        $this->assertSame($expectedPriority, $campDateAttachmentConfig->getPriority());
    }

    private function getCampDateAttachmentConfigDataTransfer(): CampDateAttachmentConfigDataTransfer
    {
        $container = static::getContainer();

        /** @var CampDateAttachmentConfigDataTransfer $dataTransfer */
        $dataTransfer = $container->get(CampDateAttachmentConfigDataTransfer::class);

        return $dataTransfer;
    }
}