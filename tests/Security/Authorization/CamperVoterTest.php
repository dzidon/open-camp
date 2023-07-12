<?php

namespace App\Tests\Security\Authorization;

use App\Enum\GenderEnum;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use App\Security\Authorization\CamperVoter;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CamperVoterTest extends KernelTestCase
{
    public function testVoteGranted(): void
    {
        $voter = $this->getCamperVoter();

        $user = new User('bob@gmail.com');
        $camper = new Camper('Name name', GenderEnum::MALE, new DateTimeImmutable('now'), $user);
        $tokenMock = $this->createTokenMock($user);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($tokenMock, $camper, ['camper_read']));
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($tokenMock, $camper, ['camper_update']));
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($tokenMock, $camper, ['camper_delete']));
    }

    public function testVoteDenied(): void
    {
        $voter = $this->getCamperVoter();

        $loggedInUser = new User('bob2@gmail.com');
        $user = new User('bob@gmail.com');
        $camper = new Camper('Name name', GenderEnum::MALE, new DateTimeImmutable('now'), $user);
        $tokenMock = $this->createTokenMock($loggedInUser);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $camper, ['camper_read']));
        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $camper, ['camper_update']));
        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $camper, ['camper_delete']));
    }

    public function testVoteInvalidAttribute(): void
    {
        $voter = $this->getCamperVoter();

        $user = new User('bob@gmail.com');
        $camper = new Camper('Name name', GenderEnum::MALE, new DateTimeImmutable('now'), $user);
        $tokenMock = $this->createTokenMock($user);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, $camper, ['']));
    }

    public function testVoteInvalidSubject(): void
    {
        $voter = $this->getCamperVoter();

        $user = new User('bob@gmail.com');
        $tokenMock = $this->createTokenMock($user);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, null, ['camper_read']));
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, null, ['camper_update']));
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, null, ['camper_delete']));
    }

    public function testVoteInvalidUser(): void
    {
        $voter = $this->getCamperVoter();

        /** @var UserInterface|MockObject $userMock */
        $userMock = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $tokenMock = $this->createTokenMock($userMock);
        $camper = new Camper('Name name', GenderEnum::MALE, new DateTimeImmutable('now'), new User('bob@gmail.com'));

        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $camper, ['camper_read']));
        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $camper, ['camper_update']));
        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $camper, ['camper_delete']));
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

    private function getCamperVoter(): CamperVoter
    {
        $container = static::getContainer();

        /** @var CamperVoter $voter */
        $voter = $container->get(CamperVoter::class);

        return $voter;
    }
}