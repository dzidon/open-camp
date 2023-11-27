<?php

namespace App\Tests\Model\EventSubscriber\Common;

use App\Model\EventSubscriber\Common\DoctrineUpdatedAtSubscriber;
use App\Tests\Model\Entity\EntityMock;
use DateTimeImmutable;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineUpdatedAtSubscriberTest extends KernelTestCase
{
    public function testPreUpdate(): void
    {
        $entity = new EntityMock('name', 'label');
        $eventMock = $this->createPreUpdateEventArgsMock($entity);
        $subscriber = $this->getDoctrineUpdatedAtSubscriber();

        $subscriber->preUpdate($eventMock);

        $this->assertNotNull($entity->getUpdatedAt());
        $this->assertSame(
            (new DateTimeImmutable('now'))->getTimestamp(),
            $entity->getUpdatedAt()->getTimestamp()
        );
    }

    private function createPreUpdateEventArgsMock(object $entity): PreUpdateEventArgs
    {
        /** @var PreUpdateEventArgs|MockObject $event */
        $event = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $event
            ->expects($this->any())
            ->method('getObject')
            ->willReturn($entity)
        ;

        return $event;
    }

    private function getDoctrineUpdatedAtSubscriber(): DoctrineUpdatedAtSubscriber
    {
        $container = static::getContainer();

        /** @var DoctrineUpdatedAtSubscriber $subscriber */
        $subscriber = $container->get(DoctrineUpdatedAtSubscriber::class);

        return $subscriber;
    }
}