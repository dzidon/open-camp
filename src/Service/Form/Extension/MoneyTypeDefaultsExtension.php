<?php

namespace App\Service\Form\Extension;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Money type extension that adds currency.
 */
class MoneyTypeDefaultsExtension extends AbstractTypeExtension
{
    private string $currency;

    public function __construct(
        #[Autowire('%app.currency%')]
        string $currency
    ) {
        $this->currency = $currency;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'currency' => $this->currency,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [MoneyType::class];
    }
}