<?php

namespace App\Tests\Service\Menu\Breadcrumbs\User;

use App\Service\Menu\Breadcrumbs\BreadcrumbsRegistryInterface;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PasswordChangeBreadcrumbsTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private BreadcrumbsRegistryInterface $breadcrumbsRegistry;

    public function testBuildPasswordChange(): void
    {
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('user_password_change');

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_login',
            'user_password_change'
        ], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_login');
        $this->assertSame(false, $profileButton->isActive());
        $this->assertSame('/login', $profileButton->getUrl());

        $userPasswordChangeButton = $breadcrumbsMenu->getChild('user_password_change');
        $this->assertSame(true, $userPasswordChangeButton->isActive());
        $this->assertSame('/password-change', $userPasswordChangeButton->getUrl());
    }

    public function testBuildPasswordChangeCompleteWhenNotAuthenticated(): void
    {
        $token = 'xyz';
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('user_password_change_complete', [
            'token'            => $token,
            'is_authenticated' => false,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_login',
            'user_password_change',
            'user_password_change_complete'
        ], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profileButton = $breadcrumbsMenu->getChild('user_login');
        $this->assertSame(false, $profileButton->isActive());
        $this->assertSame('/login', $profileButton->getUrl());

        $userPasswordChangeButton = $breadcrumbsMenu->getChild('user_password_change');
        $this->assertSame(false, $userPasswordChangeButton->isActive());
        $this->assertSame('/password-change', $userPasswordChangeButton->getUrl());

        $userPasswordChangeButton = $breadcrumbsMenu->getChild('user_password_change_complete');
        $this->assertSame(true, $userPasswordChangeButton->isActive());
        $expectedUrl = sprintf('/password-change/complete/%s', $token);
        $this->assertSame($expectedUrl, $userPasswordChangeButton->getUrl());
    }

    public function testBuildPasswordChangeCompleteWhenAuthenticated(): void
    {
        $token = 'xyz';
        $breadcrumbsMenu = $this->breadcrumbsRegistry->getBreadcrumbs('user_password_change_complete', [
            'token'            => $token,
            'is_authenticated' => true,
        ]);

        $this->assertSame('breadcrumbs', $breadcrumbsMenu->getIdentifier());
        $this->assertSame([
            'user_home',
            'user_profile_password_change_create',
            'user_password_change_complete'
        ], $this->getTreeNodeChildrenIdentifiers($breadcrumbsMenu));

        $homeButton = $breadcrumbsMenu->getChild('user_home');
        $this->assertSame(false, $homeButton->isActive());
        $this->assertSame('/', $homeButton->getUrl());

        $profilePasswordChangeCreate = $breadcrumbsMenu->getChild('user_profile_password_change_create');
        $this->assertSame(false, $profilePasswordChangeCreate->isActive());
        $this->assertSame('/profile/password-change-create', $profilePasswordChangeCreate->getUrl());

        $userPasswordChangeButton = $breadcrumbsMenu->getChild('user_password_change_complete');
        $this->assertSame(true, $userPasswordChangeButton->isActive());
        $expectedUrl = sprintf('/password-change/complete/%s', $token);
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