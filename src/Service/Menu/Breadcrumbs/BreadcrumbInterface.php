<?php

namespace App\Service\Menu\Breadcrumbs;

use App\Library\Menu\MenuTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Contains logic for building a breadcrumb link.
 */
interface BreadcrumbInterface
{
    /**
     * @return string
     */
    public function getSupportedRoute(): string;

    /**
     * @param array $options
     * @return string|null
     */
    public function getPreviousRoute(array $options): ?string;

    /**
     * Adds the breadcrumb to the given menu.
     *
     * @param MenuTypeInterface $breadcrumbs
     * @param array $options
     * @return void
     */
    public function buildBreadcrumb(MenuTypeInterface $breadcrumbs, array $options): void;

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void;
}