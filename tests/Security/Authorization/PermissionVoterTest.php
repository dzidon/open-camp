<?php

namespace App\Tests\Security\Authorization;

use App\Model\Repository\UserRepositoryInterface;
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

        $tokenMock = $this->createTokenMock($user);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, null, ['permission_non_existent']));
        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, null, ['permission1']));
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($tokenMock, null, ['permission3']));
    }

    public function testAnyPermission(): void
    {
        $voter = $this->getPermissionVoter();
        $userRepository = $this->getUserRepository();
        $user1 = $userRepository->findOneByEmail('kate@gmail.com');
        $user2 = $userRepository->findOneByEmail('david@gmail.com');
        $user3 = $userRepository->findOneByEmail('jeff@gmail.com');

        $tokenMock = $this->createTokenMock($user1);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($tokenMock, null, ['_any_permission']));

        $tokenMock = $this->createTokenMock($user2);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($tokenMock, null, ['_any_permission']));

        $tokenMock = $this->createTokenMock($user3);
        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, null, ['_any_permission']));
    }

    public function testVoteNoUser(): void
    {
        $voter = $this->getPermissionVoter();

        /** @var UserInterface|MockObject $userMock */
        $userMock = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $tokenMock = $this->createTokenMock($userMock);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, null, []));
    }

    private function createTokenMock(?UserInterface $user): TokenInterface
    {
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

        return $tokenMock;
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