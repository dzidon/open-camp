<?php

namespace App\Model\Library\DiscountConfig;

/**
 * Asserts that sale configs have the desired array shape.
 */
interface DiscountConfigArrayShapeInterface
{
    /**
     * Throws a LogicException if the given array shape is wrong.
     *
     * @param array $config
     * @return void
     */
    public function assertRecurringCampersConfig(array $config): void;

    /**
     * Throws a LogicException if the given array shape is wrong.
     *
     * @param array $config
     * @return void
     */
    public function assertSiblingsConfig(array $config): void;
}