<?php

namespace App\Tests\Model\Module\CampCatalog\Camp;

use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\CampImage;
use App\Model\Module\CampCatalog\Camp\UserCampCatalogResult;
use DateTimeImmutable;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class UserCampCatalogResultTest extends TestCase
{
    private PaginatorInterface|MockObject $paginatorMock;

    public function testInvalidPaginatorItems(): void
    {
        $this->paginatorMock
            ->expects($this->any())
            ->method('getCurrentPageItems')
            ->willReturn([new stdClass()])
        ;

        $this->expectException(LogicException::class);
        new UserCampCatalogResult($this->paginatorMock, [], []);
    }

    public function testInvalidCampImages(): void
    {
        $this->expectException(LogicException::class);
        new UserCampCatalogResult($this->paginatorMock, [new stdClass()], []);
    }

    public function testInvalidCampDates(): void
    {
        $this->expectException(LogicException::class);
        new UserCampCatalogResult($this->paginatorMock, [], [new stdClass()]);
    }

    public function testPaginator(): void
    {
        $userCatalogResult = new UserCampCatalogResult($this->paginatorMock, [], []);
        $this->assertSame($this->paginatorMock, $userCatalogResult->getPaginator());
    }

    public function testCampImage(): void
    {
        $campUnused = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $campImage = new CampImage(100, 'png', $camp);
        $campIdString = $camp
            ->getId()
            ->toRfc4122()
        ;

        $userCatalogResult = new UserCampCatalogResult($this->paginatorMock, [$campImage], []);
        $this->assertNull($userCatalogResult->getCampImage('xyz'));
        $this->assertNull($userCatalogResult->getCampImage($campUnused));
        $this->assertSame($userCatalogResult->getCampImage($campIdString), $campImage);
        $this->assertSame($userCatalogResult->getCampImage($camp), $campImage);
    }

    public function testCampDates(): void
    {
        $campUnused = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $camp = new Camp('Camp', 'camp', 5, 10, 'Street 123', 'Town', '12345', 'CS', 321);
        $campDate1 = new CampDate(new DateTimeImmutable('2000-01-01'), new DateTimeImmutable('2000-01-07'), 1000, 10, $camp);
        $campDate2 = new CampDate(new DateTimeImmutable('2000-02-02'), new DateTimeImmutable('2000-02-08'), 2000, 20, $camp);
        $campIdString = $camp
            ->getId()
            ->toRfc4122()
        ;

        $userCatalogResult = new UserCampCatalogResult($this->paginatorMock, [], [$campDate1, $campDate2]);
        $this->assertEmpty($userCatalogResult->getCampDates('xyz'));
        $this->assertEmpty($userCatalogResult->getCampDates($campUnused));
        $this->assertSame($userCatalogResult->getCampDates($campIdString), [$campDate1, $campDate2]);
        $this->assertSame($userCatalogResult->getCampDates($camp), [$campDate1, $campDate2]);
    }

    protected function setUp(): void
    {
        /** @var PaginatorInterface|MockObject $paginatorMock */
        $paginatorMock = $this->getMockBuilder(PaginatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->paginatorMock = $paginatorMock;
    }
}