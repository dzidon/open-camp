<?php

namespace App\Tests\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Library\Data\Admin\CampDateData;
use App\Library\Data\Admin\CampDateFormFieldData;
use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateAttachmentConfig;
use App\Model\Entity\CampDateFormField;
use App\Model\Entity\CampDatePurchasableItem;
use App\Model\Entity\FormField;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\User;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use App\Service\Data\Transfer\Admin\CampDateDataTransfer;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class CampDateDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getCampDateDataTransfer();

        $expectedStartAt = new DateTimeImmutable('2000-01-01');
        $expectedEndAt = new DateTimeImmutable('2000-01-05');
        $expectedPrice = 1000.0;
        $expectedCapacity = 10;
        $expectedDescription = 'Instructions...';
        $expectedLeaders = [
            new User('bob1@gmail.com'),
            new User('bob2@gmail.com'),
        ];

        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS');
        $campDate = new CampDate($expectedStartAt, $expectedEndAt, $expectedPrice, $expectedCapacity, $camp);
        $campDate->setIsClosed(true);
        $campDate->setIsOpenAboveCapacity(true);
        $campDate->setDescription($expectedDescription);

        foreach ($expectedLeaders as $expectedLeader)
        {
            $campDate->addLeader($expectedLeader);
        }

        $formField1 = new FormField('New field 1', FormFieldTypeEnum::TEXT, 'New field 1:');
        new CampDateFormField($campDate, $formField1, 100);
        $formField2 = new FormField('New field 2', FormFieldTypeEnum::NUMBER, 'New field 2:');
        new CampDateFormField($campDate, $formField2, 200);

        $purchasableItem1 = new PurchasableItem('New item 1', 1000.0, 1);
        new CampDatePurchasableItem($campDate, $purchasableItem1, 100);
        $purchasableItem2 = new PurchasableItem('New item 2', 2000.0, 2);
        new CampDatePurchasableItem($campDate, $purchasableItem2, 200);

        $attachmentConfig1 = new AttachmentConfig('New config 1', 10.0);
        new CampDateAttachmentConfig($campDate, $attachmentConfig1, 100);
        $attachmentConfig2 = new AttachmentConfig('New config 2', 20.0);
        new CampDateAttachmentConfig($campDate, $attachmentConfig2, 200);

        $data = new CampDateData($camp);
        $dataTransfer->fillData($data, $campDate);

        $this->assertSame($expectedStartAt, $data->getStartAt());
        $this->assertSame($expectedEndAt, $data->getEndAt());
        $this->assertSame($expectedPrice, $data->getPrice());
        $this->assertSame($expectedCapacity, $data->getCapacity());
        $this->assertTrue($data->isClosed());
        $this->assertTrue($data->isOpenAboveCapacity());
        $this->assertSame($expectedDescription, $data->getDescription());
        $this->assertSame($expectedLeaders, $data->getLeaders());

        $campDateFormFieldsData = $data->getCampDateFormFieldsData();
        $this->assertCount(2, $campDateFormFieldsData);
        $campDateFormFieldData1 = $campDateFormFieldsData[0];
        $this->assertSame($formField1, $campDateFormFieldData1->getFormField());
        $this->assertSame(100, $campDateFormFieldData1->getPriority());
        $campDateFormFieldData2 = $campDateFormFieldsData[1];
        $this->assertSame($formField2, $campDateFormFieldData2->getFormField());
        $this->assertSame(200, $campDateFormFieldData2->getPriority());

        $campDatePurchasableItemsData = $data->getCampDatePurchasableItemsData();
        $this->assertCount(2, $campDatePurchasableItemsData);
        $campDatePurchasableItemData1 = $campDatePurchasableItemsData[0];
        $this->assertSame($purchasableItem1, $campDatePurchasableItemData1->getPurchasableItem());
        $this->assertSame(100, $campDatePurchasableItemData1->getPriority());
        $campDatePurchasableItemData2 = $campDatePurchasableItemsData[1];
        $this->assertSame($purchasableItem2, $campDatePurchasableItemData2->getPurchasableItem());
        $this->assertSame(200, $campDatePurchasableItemData2->getPriority());

        $campDateAttachmentConfigsData = $data->getCampDateAttachmentConfigsData();
        $this->assertCount(2, $campDateAttachmentConfigsData);
        $campDateAttachmentConfigData1 = $campDateAttachmentConfigsData[0];
        $this->assertSame($attachmentConfig1, $campDateAttachmentConfigData1->getAttachmentConfig());
        $this->assertSame(100, $campDateAttachmentConfigData1->getPriority());
        $campDateAttachmentConfigData2 = $campDateAttachmentConfigsData[1];
        $this->assertSame($attachmentConfig2, $campDateAttachmentConfigData2->getAttachmentConfig());
        $this->assertSame(200, $campDateAttachmentConfigData2->getPriority());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getCampDateDataTransfer();
        $campDateRepository = $this->getCampDateRepository();
        $userRepository = $this->getUserRepository();

        $expectedStartAt = new DateTimeImmutable('3100-01-01');
        $expectedEndAt = new DateTimeImmutable('3100-01-05');
        $expectedPrice = 1000.0;
        $expectedCapacity = 10;
        $expectedDescription = 'Instructions...';

        $user1 = new User('bob1@gmail.com');
        $userRepository->saveUser($user1, false);
        $user2 = new User('bob2@gmail.com');
        $userRepository->saveUser($user2, false);
        $expectedLeaders = [$user1, $user2];

        $campDate = $campDateRepository->findOneById(new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $camp = $campDate->getCamp();

        $data = new CampDateData($camp);
        $data->setIsClosed(true);
        $data->setIsOpenAboveCapacity(true);
        $data->setStartAt($expectedStartAt);
        $data->setEndAt($expectedEndAt);
        $data->setPrice($expectedPrice);
        $data->setCapacity($expectedCapacity);
        $data->setDescription($expectedDescription);

        foreach ($expectedLeaders as $expectedLeader)
        {
            $data->addLeader($expectedLeader);
        }

        // attachment configs
        $campDateAttachmentConfigs = $campDate->getCampDateAttachmentConfigs();
        $attachmentConfig = $campDateAttachmentConfigs[0]->getAttachmentConfig();
        foreach ($campDateAttachmentConfigs as $campDateAttachmentConfig)
        {
            $priority = $campDateAttachmentConfig->getPriority();
            if ($priority === 100)
            {
                continue;
            }

            $campDateAttachmentConfigData = new CampDateAttachmentConfigData();
            $campDateAttachmentConfigData->setAttachmentConfig($campDateAttachmentConfig->getAttachmentConfig());
            $campDateAttachmentConfigData->setPriority(250);
            $data->addCampDateAttachmentConfigsDatum($campDateAttachmentConfigData);
        }

        $campDateAttachmentConfigDataNew = new CampDateAttachmentConfigData();
        $campDateAttachmentConfigDataNew->setAttachmentConfig($attachmentConfig);
        $campDateAttachmentConfigDataNew->setPriority(300);
        $data->addCampDateAttachmentConfigsDatum($campDateAttachmentConfigDataNew);

        // form fields
        $campDateFormFields = $campDate->getCampDateFormFields();
        $formField = $campDateFormFields[0]->getFormField();
        foreach ($campDateFormFields as $campDateFormField)
        {
            $priority = $campDateFormField->getPriority();
            if ($priority === 100)
            {
                continue;
            }

            $campDateFormFieldData = new CampDateFormFieldData();
            $campDateFormFieldData->setFormField($campDateFormField->getFormField());
            $campDateFormFieldData->setPriority(250);
            $data->addCampDateFormFieldsDatum($campDateFormFieldData);
        }

        $campDateFormFieldDataNew = new CampDateFormFieldData();
        $campDateFormFieldDataNew->setFormField($formField);
        $campDateFormFieldDataNew->setPriority(300);
        $data->addCampDateFormFieldsDatum($campDateFormFieldDataNew);

        // purchasable items
        $campDatePurchasableItems = $campDate->getCampDatePurchasableItems();
        $purchasableItem = $campDatePurchasableItems[0]->getPurchasableItem();
        foreach ($campDatePurchasableItems as $campDatePurchasableItem)
        {
            $priority = $campDatePurchasableItem->getPriority();
            if ($priority === 100)
            {
                continue;
            }

            $campDatePurchasableItemData = new CampDatePurchasableItemData();
            $campDatePurchasableItemData->setPurchasableItem($campDatePurchasableItem->getPurchasableItem());
            $campDatePurchasableItemData->setPriority(250);
            $data->addCampDatePurchasableItemsDatum($campDatePurchasableItemData);
        }

        $campDatePurchasableItemDataNew = new CampDatePurchasableItemData();
        $campDatePurchasableItemDataNew->setPurchasableItem($purchasableItem);
        $campDatePurchasableItemDataNew->setPriority(300);
        $data->addCampDatePurchasableItemsDatum($campDatePurchasableItemDataNew);

        $dataTransfer->fillEntity($data, $campDate);

        $this->assertSame($expectedStartAt, $campDate->getStartAt());
        $this->assertSame($expectedEndAt, $campDate->getEndAt());
        $this->assertSame($expectedPrice, $campDate->getPrice());
        $this->assertSame($expectedCapacity, $campDate->getCapacity());
        $this->assertTrue($campDate->isClosed());
        $this->assertTrue($campDate->isOpenAboveCapacity());
        $this->assertSame($expectedDescription, $campDate->getDescription());
        $this->assertSame($expectedLeaders, $campDate->getLeaders());

        $campDateRepository->saveCampDate($campDate, true);
        $campDate = $campDateRepository->findOneById(new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $priorities = $this->getCampDateAttachmentConfigPriorities($campDate->getCampDateAttachmentConfigs());
        $this->assertCount(2, $priorities);
        $this->assertContains(250, $priorities);
        $this->assertContains(300, $priorities);

        $priorities = $this->getCampDateFormFieldPriorities($campDate->getCampDateFormFields());
        $this->assertCount(2, $priorities);
        $this->assertContains(250, $priorities);
        $this->assertContains(300, $priorities);

        $priorities = $this->getCampDatePurchasableItemPriorities($campDate->getCampDatePurchasableItems());
        $this->assertCount(2, $priorities);
        $this->assertContains(250, $priorities);
        $this->assertContains(300, $priorities);
    }

    private function getCampDateAttachmentConfigPriorities(array $campDateAttachmentConfigs): array
    {
        $priorities = [];

        /** @var CampDateAttachmentConfig $campDateAttachmentConfig */
        foreach ($campDateAttachmentConfigs as $campDateAttachmentConfig)
        {
            $priorities[] = $campDateAttachmentConfig->getPriority();
        }

        return $priorities;
    }

    private function getCampDatePurchasableItemPriorities(array $campDatePurchasableItems): array
    {
        $priorities = [];

        /** @var CampDatePurchasableItem $campDatePurchasableItem */
        foreach ($campDatePurchasableItems as $campDatePurchasableItem)
        {
            $priorities[] = $campDatePurchasableItem->getPriority();
        }

        return $priorities;
    }

    private function getCampDateFormFieldPriorities(array $campDateFormFields): array
    {
        $priorities = [];

        /** @var CampDateFormField $campDateFormField */
        foreach ($campDateFormFields as $campDateFormField)
        {
            $priorities[] = $campDateFormField->getPriority();
        }

        return $priorities;
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $repository */
        $repository = $container->get(UserRepositoryInterface::class);

        return $repository;
    }

    private function getCampDateRepository(): CampDateRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampDateRepositoryInterface $repository */
        $repository = $container->get(CampDateRepositoryInterface::class);

        return $repository;
    }

    private function getCampDateDataTransfer(): CampDateDataTransfer
    {
        $container = static::getContainer();

        /** @var CampDateDataTransfer $dataTransfer */
        $dataTransfer = $container->get(CampDateDataTransfer::class);

        return $dataTransfer;
    }
}