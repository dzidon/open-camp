<?php

namespace App\Tests\Functional\Search\Paginator;

use App\Search\Paginator\Paginator;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests paginator calculations. Method 'getCurrentPageItems' is excluded, this test does not fetch
 * items from the database. Correct retrieval of items is tested in individual repository tests.
 */
class PaginatorTest extends TestCase
{
    public function testGetTotalItems(): void
    {
        $paginator = $this->createPaginator(10, 1, 10);
        $this->assertSame(10, $paginator->getTotalItems());
    }

    public function testGetPageSize(): void
    {
        $paginator = $this->createPaginator(5, 1, 5);
        $this->assertSame(5, $paginator->getPageSize());
    }

    public function testGetCurrentPage(): void
    {
        $paginator = $this->createPaginator(5, 0, 5); // sets current page to 1
        $this->assertSame(1, $paginator->getCurrentPage());

        $paginator = $this->createPaginator(5, 1, 5);
        $this->assertSame(1, $paginator->getCurrentPage());

        $paginator = $this->createPaginator(10, 2, 5);
        $this->assertSame(2, $paginator->getCurrentPage());
    }

    public function testGetPagesCount(): void
    {
        $paginator = $this->createPaginator(0, 1, 5);
        $this->assertSame(0, $paginator->getPagesCount());

        $paginator = $this->createPaginator(5, 1, 5);
        $this->assertSame(1, $paginator->getPagesCount());

        $paginator = $this->createPaginator(10, 1, 5);
        $this->assertSame(2, $paginator->getPagesCount());

        $paginator = $this->createPaginator(5, 1, 3);
        $this->assertSame(2, $paginator->getPagesCount());
    }

    public function testIsPageOutOfBounds(): void
    {
        $paginator = $this->createPaginator(0, 1, 5);
        $this->assertSame(true, $paginator->isPageOutOfBounds(0));
        $this->assertSame(false, $paginator->isPageOutOfBounds(1));
        $this->assertSame(true, $paginator->isPageOutOfBounds(2));

        $paginator = $this->createPaginator(5, 1, 5);
        $this->assertSame(true, $paginator->isPageOutOfBounds(0));
        $this->assertSame(false, $paginator->isPageOutOfBounds(1));
        $this->assertSame(true, $paginator->isPageOutOfBounds(2));

        $paginator = $this->createPaginator(10, 1, 5);
        $this->assertSame(true, $paginator->isPageOutOfBounds(0));
        $this->assertSame(false, $paginator->isPageOutOfBounds(1));
        $this->assertSame(false, $paginator->isPageOutOfBounds(2));
        $this->assertSame(true, $paginator->isPageOutOfBounds(3));

        $paginator = $this->createPaginator(5, 1, 3);
        $this->assertSame(true, $paginator->isPageOutOfBounds(0));
        $this->assertSame(false, $paginator->isPageOutOfBounds(1));
        $this->assertSame(false, $paginator->isPageOutOfBounds(2));
        $this->assertSame(true, $paginator->isPageOutOfBounds(3));
    }

    public function testIsCurrentPageOutOfBounds(): void
    {
        $paginator = $this->createPaginator(0, 0, 5); // sets current page to 1
        $this->assertSame(false, $paginator->isCurrentPageOutOfBounds());
        $paginator = $this->createPaginator(0, 1, 5);
        $this->assertSame(false, $paginator->isCurrentPageOutOfBounds());
        $paginator = $this->createPaginator(0, 2, 5);
        $this->assertSame(true, $paginator->isCurrentPageOutOfBounds());

        $paginator = $this->createPaginator(5, 0, 5); // sets current page to 1
        $this->assertSame(false, $paginator->isCurrentPageOutOfBounds());
        $paginator = $this->createPaginator(5, 1, 5);
        $this->assertSame(false, $paginator->isCurrentPageOutOfBounds());
        $paginator = $this->createPaginator(5, 2, 5);
        $this->assertSame(true, $paginator->isCurrentPageOutOfBounds());

        $paginator = $this->createPaginator(10, 0, 5); // sets current page to 1
        $this->assertSame(false, $paginator->isCurrentPageOutOfBounds());
        $paginator = $this->createPaginator(10, 1, 5);
        $this->assertSame(false, $paginator->isCurrentPageOutOfBounds());
        $paginator = $this->createPaginator(10, 2, 5);
        $this->assertSame(false, $paginator->isCurrentPageOutOfBounds());
        $paginator = $this->createPaginator(10, 3, 5);
        $this->assertSame(true, $paginator->isCurrentPageOutOfBounds());

        $paginator = $this->createPaginator(5, 0, 3); // sets current page to 1
        $this->assertSame(false, $paginator->isCurrentPageOutOfBounds());
        $paginator = $this->createPaginator(5, 1, 3);
        $this->assertSame(false, $paginator->isCurrentPageOutOfBounds());
        $paginator = $this->createPaginator(5, 2, 3);
        $this->assertSame(false, $paginator->isCurrentPageOutOfBounds());
        $paginator = $this->createPaginator(5, 3, 3);
        $this->assertSame(true, $paginator->isCurrentPageOutOfBounds());
    }

    /**
     * Creates an instance of the paginator. Uses a mocked instance of Doctrine\ORM\Tools\Pagination\Paginator.
     *
     * @param int $totalItems
     * @param int $currentPage
     * @param int $pageSize
     * @return Paginator
     */
    private function createPaginator(int $totalItems, int $currentPage, int $pageSize): Paginator
    {
        // query mock
        /** @var AbstractQuery|MockObject $queryMock */
        $queryMock = $this->getMockBuilder(AbstractQuery::class)
            ->setMethods(['getResult', 'setFirstResult', 'setMaxResults'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;

        $queryMock
            ->expects($this->any())
            ->method('getResult')
            ->willReturn([])
        ;

        $queryMock
            ->expects($this->any())
            ->method('setFirstResult')
            ->willReturn($queryMock)
        ;

        $queryMock
            ->expects($this->any())
            ->method('setMaxResults')
            ->willReturn($queryMock)
        ;

        // doctrine paginator mock
        /** @var DoctrinePaginator|MockObject $doctrinePaginatorMock */
        $doctrinePaginatorMock = $this->getMockBuilder(DoctrinePaginator::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $doctrinePaginatorMock
            ->expects($this->any())
            ->method('count')
            ->willReturn($totalItems)
        ;

        $doctrinePaginatorMock
            ->expects($this->any())
            ->method('getQuery')
            ->willReturn($queryMock)
        ;

        return new Paginator($doctrinePaginatorMock, $currentPage, $pageSize);
    }
}