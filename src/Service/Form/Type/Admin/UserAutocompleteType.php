<?php

namespace App\Service\Form\Type\Admin;

use App\Model\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

/**
 * User AJAX autocomplete.
 */
#[AsEntityAutocompleteField]
class UserAutocompleteType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class'          => User::class,
            'min_characters' => 2,
            'filter_query'   => function(QueryBuilder $queryBuilder, string $phrase) {
                $queryBuilder
                    ->andWhere('entity.email LIKE :phrase OR CONCAT(entity.nameFirst, \' \', entity.nameLast) LIKE :phrase')
                    ->setParameter('phrase', '%' . $phrase . '%')
                    ->orderBy('entity.createdAt', 'DESC')
                ;
            },
            'security' => function(Security $security): bool {
                return $security->isGranted('camp_create') || $security->isGranted('camp_update');
            },
            'choice_label' => function (User $user) {
                $label = $user->getEmail();
                $nameFirst = $user->getNameFirst();
                $nameLast = $user->getNameLast();

                if ($nameFirst !== null && $nameLast !== null)
                {
                    $label .= sprintf(' (%s %s)', $nameFirst, $nameLast);
                }

                return $label;
            },
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}