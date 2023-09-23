<?php

namespace App\Tests\Library\Data\User;

use App\Library\Data\User\PlainPasswordData;
use App\Library\Data\User\ProfilePasswordChangeData;
use App\Model\Entity\User;
use App\Model\Repository\UserRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfilePasswordChangeDataTest extends KernelTestCase
{
    public function testCurrentPassword(): void
    {
        $data = new ProfilePasswordChangeData();
        $this->assertNull($data->getCurrentPassword());

        $data->setCurrentPassword('text');
        $this->assertSame('text', $data->getCurrentPassword());

        $data->setCurrentPassword(null);
        $this->assertNull($data->getCurrentPassword());
    }

    public function testCurrentPasswordValidation(): void
    {
        $validator = $this->getValidator();
        $user = $this->createUser();
        $this->setMockedTokenStorageWithUser($user);

        $data = new ProfilePasswordChangeData();
        $result = $validator->validateProperty($data, 'currentPassword');
        $this->assertNotEmpty($result); // invalid

        $data->setCurrentPassword('123');
        $result = $validator->validateProperty($data, 'currentPassword');
        $this->assertNotEmpty($result); // invalid

        $data->setCurrentPassword('123456');
        $result = $validator->validateProperty($data, 'currentPassword');
        $this->assertEmpty($result); // valid
    }

    public function testNewPasswordData(): void
    {
        $data = new ProfilePasswordChangeData();
        $newPasswordData = $data->getNewPasswordData();
        $this->assertInstanceOf(PlainPasswordData::class, $newPasswordData);
    }

    public function testNewPasswordDataValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ProfilePasswordChangeData();
        $result = $validator->validateProperty($data, 'newPasswordData');
        $this->assertNotEmpty($result); // invalid

        $newPasswordData = $data->getNewPasswordData();
        $newPasswordData->setPlainPassword('123456');
        $result = $validator->validateProperty($data, 'newPasswordData');
        $this->assertEmpty($result); // valid
    }

    private function createUser(): User
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $container->get(UserRepositoryInterface::class);

        return $userRepository->createUser('bob@gmail.com', '123456');
    }

    private function setMockedTokenStorageWithUser(User $user): void
    {
        $container = static::getContainer();

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

        /** @var TokenStorageInterface|MockObject $sessionMock */
        $storageMock = $this->getMockBuilder(TokenStorageInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $storageMock
            ->expects($this->any())
            ->method('getToken')
            ->willReturn($tokenMock)
        ;

        $container->set(TokenStorageInterface::class, $storageMock);
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}