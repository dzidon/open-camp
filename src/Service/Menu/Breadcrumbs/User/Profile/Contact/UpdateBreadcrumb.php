<?php

namespace App\Service\Menu\Breadcrumbs\User\Profile\Contact;

use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\Contact;
use App\Service\Menu\Breadcrumbs\AbstractBreadcrumb;
use App\Service\Menu\Breadcrumbs\BreadcrumbInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateBreadcrumb extends AbstractBreadcrumb implements BreadcrumbInterface
{
    public function getSupportedRoute(): string
    {
        return 'user_profile_contact_update';
    }

    public function getPreviousRoute(array $options): ?string
    {
        return 'user_profile_contact_list';
    }

    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void
    {
        /** @var Contact $contact */
        $contact = $options['contact'];

        $this->addRoute($breadcrumbs, 'user_profile_contact_update', [
            'id' => $contact->getId(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('contact');
        $resolver->setAllowedTypes('contact', Contact::class);
        $resolver->setRequired('contact');
    }
}