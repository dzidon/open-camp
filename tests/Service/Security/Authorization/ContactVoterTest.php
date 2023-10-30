<?php

namespace App\Tests\Service\Security\Authorization;

use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Model\Enum\Entity\ContactRoleEnum;
use App\Service\Security\Authorization\ContactVoter;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactVoterTest extends KernelTestCase
{
    public function testVoteGranted(): void
    {
        $voter = $this->getContactVoter();

        $user = new User('bob@gmail.com');
        $contact = new Contact('Name', 'bob@gmail.com', $user, ContactRoleEnum::MOTHER);
        $tokenMock = $this->createTokenMock($user);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($tokenMock, $contact, ['contact_read']));
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($tokenMock, $contact, ['contact_update']));
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($tokenMock, $contact, ['contact_delete']));
    }

    public function testVoteDenied(): void
    {
        $voter = $this->getContactVoter();

        $loggedInUser = new User('bob2@gmail.com');
        $user = new User('bob@gmail.com');
        $contact = new Contact('Name', 'bob@gmail.com', $user, ContactRoleEnum::MOTHER);
        $tokenMock = $this->createTokenMock($loggedInUser);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $contact, ['contact_read']));
        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $contact, ['contact_update']));
        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $contact, ['contact_delete']));
    }

    public function testVoteInvalidAttribute(): void
    {
        $voter = $this->getContactVoter();

        $user = new User('bob@gmail.com');
        $contact = new Contact('Name', 'bob@gmail.com', $user, ContactRoleEnum::MOTHER);
        $tokenMock = $this->createTokenMock($user);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, $contact, ['']));
    }

    public function testVoteInvalidSubject(): void
    {
        $voter = $this->getContactVoter();

        $user = new User('bob@gmail.com');
        $tokenMock = $this->createTokenMock($user);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, null, ['contact_read']));
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, null, ['contact_update']));
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote($tokenMock, null, ['contact_delete']));
    }

    public function testVoteInvalidUser(): void
    {
        $voter = $this->getContactVoter();

        /** @var UserInterface|MockObject $userMock */
        $userMock = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $tokenMock = $this->createTokenMock($userMock);
        $contact = new Contact('Name', 'bob@gmail.com', new User('bob@gmail.com'), ContactRoleEnum::MOTHER);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $contact, ['contact_read']));
        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $contact, ['contact_update']));
        $this->assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($tokenMock, $contact, ['contact_delete']));
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

    private function getContactVoter(): ContactVoter
    {
        $container = static::getContainer();

        /** @var ContactVoter $voter */
        $voter = $container->get(ContactVoter::class);

        return $voter;
    }
}