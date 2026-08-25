<?php

declare(strict_types=1);

namespace Intervention\Image\Random;

use Random\Randomizer;

/**
 * Generates random floating-point values using the gamma-section algorithm.
 *
 * @internal
 * @see https://doi.org/10.1145/3503512
 * @see https://github.com/php/php-src/blob/PHP-8.3/ext/random/gammasection.c
 */
final class GammaSection
{
    /**
     * Generate a random float in the right-open interval from zero to the given maximum.
     */
    public static function closedOpen(Randomizer $randomizer, float $max): float
    {
        $gamma = $max - self::previousFloat($max);
        $steps = (int) ceil($max / $gamma);
        $step = 1 + $randomizer->getInt(0, $steps - 1);

        if ($step === $steps) {
            return 0.0;
        }

        $stepHigh = intdiv($step, 4);
        $stepLow = $step & 0x3;

        return 4.0 * ($max * 0.25 - $stepHigh * $gamma) - $stepLow * $gamma;
    }

    /**
     * Return the preceding representable floating-point value.
     */
    private static function previousFloat(float $value): float
    {
        $bytes = pack('E', $value);

        for ($index = strlen($bytes) - 1; $index >= 0; $index--) {
            $byte = ord($bytes[$index]);

            if ($byte > 0) {
                $bytes[$index] = chr($byte - 1);

                break;
            }

            $bytes[$index] = "\xff";
        }

        return unpack('Evalue', $bytes)['value'];
    }
}
