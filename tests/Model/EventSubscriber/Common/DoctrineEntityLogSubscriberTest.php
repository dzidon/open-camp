<?php

namespace App\Tests\Model\EventSubscriber\Common;

use App\Model\EventSubscriber\Common\DoctrineEntityLogSubscriber;
use App\Service\Logger\ModelLogger;
use App\Tests\Model\Entity\EntityMock;
use App\Tests\Service\Logger\LoggerMock;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

class DoctrineEntityLogSubscriberTest extends KernelTestCase
{
    private DoctrineEntityLogSubscriber $doctrineEntityLogSubscriber;
    
    private LoggerMock $loggerMock;
    
    private PreUpdateEventArgs $preUpdateEventArgs;

    private LifecycleEventArgs $lifecycleEventArgs;
    
    public function testPrePersist(): void
    {
        $this->doctrineEntityLogSubscriber->prePersist($this->lifecycleEventArgs);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('info', [
            'message' => 'Created EntityMock.',
            'context' => [
                'entity' => '{"name":"name","label":"label","createdAt":"2000-01-01T12:00:00+01:00","updatedAt":null}'
            ],
        ]));
    }

    public function testPreUpdate(): void
    {
        $this->doctrineEntityLogSubscriber->preUpdate($this->preUpdateEventArgs);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('info', [
            'message' => 'Updated EntityMock.',
            'context' => [
                'changeSet' => '{"name":["name","new name"],"label":["label","new label"]}'
            ],
        ]));
    }

    public function testPreRemove(): void
    {
        $this->doctrineEntityLogSubscriber->preRemove($this->lifecycleEventArgs);

        $this->assertTrue($this->loggerMock->hasLoggedMessage('info', [
            'message' => 'Deleted EntityMock.',
            'context' => [
                'entity' => '{"name":"name","label":"label","createdAt":"2000-01-01T12:00:00+01:00","updatedAt":null}'
            ],
        ]));
    }

    protected function setUp(): void
    {
        $entityMock = new EntityMock('name', 'label');
        
        // update event
        $this->preUpdateEventArgs = $this->getMockBuilder(PreUpdateEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->preUpdateEventArgs
            ->expects($this->any())
            ->method('getObject')
            ->willReturn($entityMock)
        ;

        $this->preUpdateEventArgs
            ->expects($this->any())
            ->method('getEntityChangeSet')
            ->willReturn([
                'name'  => ['name', 'new name'],
                'label' => ['label', 'new label'],
            ])
        ;

        // create & delete event
        $this->lifecycleEventArgs = $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->lifecycleEventArgs
            ->expects($this->any())
            ->method('getObject')
            ->willReturn($entityMock)
        ;

        // services
        $container = static::getContainer();
        $this->loggerMock = new LoggerMock();

        /** @var SerializerInterface $serializer */
        $serializer = $container->get(SerializerInterface::class);
        $modelLogger = new ModelLogger($this->loggerMock, $serializer);
        $this->doctrineEntityLogSubscriber = new DoctrineEntityLogSubscriber($modelLogger);
    }
}