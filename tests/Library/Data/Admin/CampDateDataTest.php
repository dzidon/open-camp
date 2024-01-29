<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\CampDateAttachmentConfigData;
use App\Library\Data\Admin\CampDateData;
use App\Library\Data\Admin\CampDateFormFieldData;
use App\Library\Data\Admin\CampDatePurchasableItemData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\FormField;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\TripLocationPath;
use App\Model\Enum\Entity\FormFieldTypeEnum;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CampDateDataTest extends KernelTestCase
{
    private Camp $camp;

    public function testCampDate(): void
    {
        $data = new CampDateData($this->camp);
        $this->assertNull($data->getCampDate());

        $campDate = new CampDate(new DateTimeImmutable('now'), new DateTimeImmutable('now'), 100.0, 200.0, 10, $this->camp);

        $data = new CampDateData($this->camp, $campDate);
        $this->assertSame($campDate, $data->getCampDate());
    }

    public function testCamp(): void
    {
        $camp = new Camp('Camp', 'camp', 5, 10, 321);
        $data = new CampDateData($camp);
        $this->assertSame($camp, $data->getCamp());
    }

    public function testStartAt(): void
    {
        $data = new CampDateData($this->camp);
        $this->assertNull($data->getStartAt());

        $expectedDateStart = new DateTimeImmutable('now');
        $data->setStartAt($expectedDateStart);
        $this->assertSame($expectedDateStart, $data->getStartAt());

        $data->setStartAt(null);
        $this->assertNull($data->getStartAt());
    }

    public function testStartAtValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData($this->camp);
        $result = $validator->validateProperty($data, 'startAt');
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('now'));
        $result = $validator->validateProperty($data, 'startAt');
        $this->assertEmpty($result); // valid
    }

    public function testEndAt(): void
    {
        $data = new CampDateData($this->camp);
        $this->assertNull($data->getEndAt());

        $expectedDateEnd = new DateTimeImmutable('now');
        $data->setEndAt($expectedDateEnd);
        $this->assertSame($expectedDateEnd, $data->getEndAt());

        $data->setEndAt(null);
        $this->assertNull($data->getEndAt());
    }

    public function testEndAtValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData($this->camp);
        $result = $validator->validateProperty($data, 'endAt');
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('now'));

        $data->setEndAt(new DateTimeImmutable('now'));
        $result = $validator->validateProperty($data, 'endAt');
        $this->assertEmpty($result); // valid

        $data->setEndAt(new DateTimeImmutable('3000-01-01'));
        $result = $validator->validateProperty($data, 'endAt');
        $this->assertEmpty($result); // valid

        $data->setEndAt(new DateTimeImmutable('2000-01-01'));
        $result = $validator->validateProperty($data, 'endAt');
        $this->assertNotEmpty($result); // invalid
    }

    public function testDeposit(): void
    {
        $data = new CampDateData($this->camp);
        $this->assertNull($data->getDeposit());

        $data->setDeposit(100.5);
        $this->assertSame(100.5, $data->getDeposit());

        $data->setDeposit(null);
        $this->assertNull($data->getDeposit());
    }

    public function testDepositValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData($this->camp);
        $result = $validator->validateProperty($data, 'deposit');
        $this->assertNotEmpty($result); // invalid

        $data->setDeposit(-1.0);
        $result = $validator->validateProperty($data, 'deposit');
        $this->assertNotEmpty($result); // invalid

        $data->setDeposit(0.0);
        $result = $validator->validateProperty($data, 'deposit');
        $this->assertEmpty($result); // valid

        $data->setDeposit(1.0);
        $result = $validator->validateProperty($data, 'deposit');
        $this->assertEmpty($result); // valid
    }

    public function testPriceWithoutDeposit(): void
    {
        $data = new CampDateData($this->camp);
        $this->assertNull($data->getPriceWithoutDeposit());

        $data->setPriceWithoutDeposit(100.5);
        $this->assertSame(100.5, $data->getPriceWithoutDeposit());

        $data->setPriceWithoutDeposit(null);
        $this->assertNull($data->getPriceWithoutDeposit());
    }

    public function testPriceWithoutDepositValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData($this->camp);
        $result = $validator->validateProperty($data, 'priceWithoutDeposit');
        $this->assertNotEmpty($result); // invalid

        $data->setPriceWithoutDeposit(-1.0);
        $result = $validator->validateProperty($data, 'priceWithoutDeposit');
        $this->assertNotEmpty($result); // invalid

        $data->setPriceWithoutDeposit(0.0);
        $result = $validator->validateProperty($data, 'priceWithoutDeposit');
        $this->assertEmpty($result); // valid

        $data->setPriceWithoutDeposit(1.0);
        $result = $validator->validateProperty($data, 'priceWithoutDeposit');
        $this->assertEmpty($result); // valid
    }

    public function testCapacity(): void
    {
        $data = new CampDateData($this->camp);
        $this->assertNull($data->getCapacity());

        $data->setCapacity(100);
        $this->assertSame(100, $data->getCapacity());

        $data->setCapacity(null);
        $this->assertNull($data->getCapacity());
    }

    public function testCapacityValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData($this->camp);
        $result = $validator->validateProperty($data, 'capacity');
        $this->assertNotEmpty($result); // invalid

        $data->setCapacity(0);
        $result = $validator->validateProperty($data, 'capacity');
        $this->assertNotEmpty($result); // invalid

        $data->setCapacity(1);
        $result = $validator->validateProperty($data, 'capacity');
        $this->assertEmpty($result); // valid
    }

    public function testIsClosed(): void
    {
        $data = new CampDateData($this->camp);
        $this->assertFalse($data->isClosed());

        $data->setIsClosed(true);
        $this->assertTrue($data->isClosed());

        $data->setIsClosed(false);
        $this->assertFalse($data->isClosed());
    }

    public function testIsOpenAboveCapacity(): void
    {
        $data = new CampDateData($this->camp);
        $this->assertFalse($data->isOpenAboveCapacity());

        $data->setIsOpenAboveCapacity(true);
        $this->assertTrue($data->isOpenAboveCapacity());

        $data->setIsOpenAboveCapacity(false);
        $this->assertFalse($data->isOpenAboveCapacity());
    }

    public function testDescription(): void
    {
        $data = new CampDateData($this->camp);
        $this->assertNull($data->getDescription());

        $data->setDescription('text');
        $this->assertSame('text', $data->getDescription());

        $data->setDescription(null);
        $this->assertNull($data->getDescription());
    }

    public function testDescriptionValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData($this->camp);
        $result = $validator->validateProperty($data, 'description');
        $this->assertEmpty($result); // valid

        $data->setDescription('');
        $result = $validator->validateProperty($data, 'description');
        $this->assertEmpty($result); // valid

        $data->setDescription(null);
        $result = $validator->validateProperty($data, 'description');
        $this->assertEmpty($result); // valid

        $data->setDescription(str_repeat('x', 2000));
        $result = $validator->validateProperty($data, 'description');
        $this->assertEmpty($result); // valid

        $data->setDescription(str_repeat('x', 2001));
        $result = $validator->validateProperty($data, 'description');
        $this->assertNotEmpty($result); // invalid
    }

    public function testTripLocationPathThere(): void
    {
        $data = new CampDateData($this->camp);
        $this->assertNull($data->getTripLocationPathThere());

        $tripLocationPath = new TripLocationPath('Path');
        $data->setTripLocationPathThere($tripLocationPath);
        $this->assertSame($tripLocationPath, $data->getTripLocationPathThere());

        $data->setTripLocationPathThere(null);
        $this->assertNull($data->getTripLocationPathThere());
    }

    public function testTripLocationPathBack(): void
    {
        $data = new CampDateData($this->camp);
        $this->assertNull($data->getTripLocationPathBack());

        $tripLocationPath = new TripLocationPath('Path');
        $data->setTripLocationPathBack($tripLocationPath);
        $this->assertSame($tripLocationPath, $data->getTripLocationPathBack());

        $data->setTripLocationPathBack(null);
        $this->assertNull($data->getTripLocationPathBack());
    }

    public function tesCampDateFormFieldsDataValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData($this->camp);
        $result = $validator->validateProperty($data, 'campDateFormFieldsData');
        $this->assertNotEmpty($result); // invalid

        $campDateFormFieldData = new CampDateFormFieldData();
        $data->addCampDateFormFieldData($campDateFormFieldData);
        $result = $validator->validateProperty($data, 'campDateFormFieldsData');
        $this->assertNotEmpty($result); // invalid

        $formField = new FormField('Field', FormFieldTypeEnum::TEXT, 'Field:');
        $campDateFormFieldData->setFormField($formField);
        $campDateFormFieldData->setPriority(1);
        $result = $validator->validateProperty($data, 'campDateFormFieldsData');
        $this->assertEmpty($result); // valid
    }

    public function tesCampDateAttachmentConfigsDataValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData($this->camp);
        $result = $validator->validateProperty($data, 'campDateAttachmentConfigsData');
        $this->assertNotEmpty($result); // invalid

        $campDateAttachmentConfigData = new CampDateAttachmentConfigData();
        $data->addCampDateAttachmentConfigData($campDateAttachmentConfigData);
        $result = $validator->validateProperty($data, 'campDateAttachmentConfigsData');
        $this->assertNotEmpty($result); // invalid

        $attachmentConfig = new AttachmentConfig('Config', 'Label', 10.0);
        $campDateAttachmentConfigData->setAttachmentConfig($attachmentConfig);
        $campDateAttachmentConfigData->setPriority(1);
        $result = $validator->validateProperty($data, 'campDateAttachmentConfigsData');
        $this->assertEmpty($result); // valid
    }

    public function tesCampDatePurchasableItemsDataValidation(): void
    {
        $validator = $this->getValidator();

        $data = new CampDateData($this->camp);
        $result = $validator->validateProperty($data, 'campDatePurchasableItemsData');
        $this->assertNotEmpty($result); // invalid

        $campDatePurchasableItemData = new CampDatePurchasableItemData();
        $data->addCampDatePurchasableItemData($campDatePurchasableItemData);
        $result = $validator->validateProperty($data, 'campDatePurchasableItemsData');
        $this->assertNotEmpty($result); // invalid

        $purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 2);
        $campDatePurchasableItemData->setPurchasableItem($purchasableItem);
        $campDatePurchasableItemData->setPriority(1);
        $result = $validator->validateProperty($data, 'campDatePurchasableItemsData');
        $this->assertEmpty($result); // valid
    }

    public function testIntervalCollision(): void
    {
        $validator = $this->getValidator();
        $campRepository = $this->getCampRepository();
        $campDateRepository = $this->getCampDateRepository();
        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $camp = $campRepository->findOneById($uid);

        $data = new CampDateData($camp);
        $data->setDeposit(100.0);
        $data->setPriceWithoutDeposit(100.0);
        $data->setDepositUntil(new DateTimeImmutable('3000-01-01'));
        $data->setCapacity(5);

        $data->setStartAt(new DateTimeImmutable('3000-01-01'));
        $data->setEndAt(new DateTimeImmutable('3000-01-02'));
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setStartAt(new DateTimeImmutable('2000-06-20'));
        $data->setEndAt(new DateTimeImmutable('2000-07-03'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-07-06'));
        $data->setEndAt(new DateTimeImmutable('2000-07-08'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-06-30'));
        $data->setEndAt(new DateTimeImmutable('2000-07-08'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-07-02'));
        $data->setEndAt(new DateTimeImmutable('2000-07-06'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-06-28'));
        $data->setEndAt(new DateTimeImmutable('2000-07-01'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-07-07'));
        $data->setEndAt(new DateTimeImmutable('2000-07-10'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setStartAt(new DateTimeImmutable('2000-07-01'));
        $data->setEndAt(new DateTimeImmutable('2000-07-07'));
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $campDate = $campDateRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateData($camp, $campDate);
        $data->setDeposit(100.0);
        $data->setPriceWithoutDeposit(100.0);
        $data->setDepositUntil(new DateTimeImmutable('3000-01-01'));
        $data->setCapacity(5);

        $data->setStartAt(new DateTimeImmutable('2000-06-20'));
        $data->setEndAt(new DateTimeImmutable('2000-07-03'));
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    protected function setUp(): void
    {
        $this->camp = new Camp('Camp', 'camp', 5, 10, 321);
    }

    private function getCampRepository(): CampRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampRepositoryInterface $repository */
        $repository = $container->get(CampRepositoryInterface::class);

        return $repository;
    }

    private function getCampDateRepository(): CampDateRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampDateRepositoryInterface $repository */
        $repository = $container->get(CampDateRepositoryInterface::class);

        return $repository;
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}