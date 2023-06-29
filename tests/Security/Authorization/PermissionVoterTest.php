<?php

namespace App\Tests\Security\Authorization;

use App\Repository\UserRepositoryInterface;
use App\Security\Authorization\PermissionVoter;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PermissionVoterTest extends KernelTestCase
{
    public function testVote(): void
    {
        $voter = $this->getPermissionVoter();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('kate@gmail.com');

        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $tokenMock
            ->expects($this->any())
            ->method('getUser')
            ->willReturn($user)
        ;

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, null, ['permission_non_existent']));

        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, null, ['permission1']));

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($tokenMock, null, ['permission3']));
    }

    public function testVoteNoUser(): void
    {
        $voter = $this->getPermissionVoter();

        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        /** @var UserInterface|MockObject $userMock */
        $userMock = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $tokenMock
            ->expects($this->any())
            ->method('getUser')
            ->willReturn($userMock)
        ;

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, null, []));
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $repository */
        $repository = $container->get(UserRepositoryInterface::class);

        return $repository;
    }

    private function getPermissionVoter(): PermissionVoter
    {
        $container = static::getContainer();

        /** @var PermissionVoter $voter */
        $voter = $container->get(PermissionVoter::class);

        return $voter;
    }
}