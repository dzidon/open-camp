<?php

namespace App\Tests\Service\Menu\Breadcrumbs\User;

use App\Service\Menu\Breadcrumbs\BreadcrumbsRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RegistrationBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private BreadcrumbsRegistryInterface $breadcrumbsRegistry;

    public function testBuildRegistration(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('user_registration');

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_registration',
        ], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_registration');
        $this->assertSame(true, $profileButton->isActive());
        $this->assertSame('/registration', $profileButton->getUrl());
    }

    public function testBuildRegistrationComplete(): void
    {
        $token = 'xyz';
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('user_registration_complete', [
            'token' => $token,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_registration',
            'user_registration_complete',
        ], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_registration');
        $this->assertSame(false, $profileButton->isActive());
        $this->assertSame('/registration', $profileButton->getUrl());

        $userPasswordChangeButton = $breadcrumbsMenu->getChild('user_registration_complete');
        $this->assertSame(true, $userPasswordChangeButton->isActive());
        $expectedUrl = sprintf('/registration/complete/%s', $token);
        $this->assertSame($expectedUrl, $userPasswordChangeButton->getUrl());
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var BreadcrumbsRegistryInterface $breadcrumbsRegistry */
        $breadcrumbsRegistry = $container->get(BreadcrumbsRegistryInterface::class);
        $this->breadcrumbsRegistry = $breadcrumbsRegistry;
    }
}