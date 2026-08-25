<?php

declare(strict_types=1);

namespace Intervention\Image\Tests\Unit;

use Generator;
use Intervention\Image\Random\GammaSection;
use Intervention\Image\Tests\BaseTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Random\Engine\Mt19937;
use Random\Randomizer;

#[CoversClass(GammaSection::class)]
final class GammaSectionTest extends BaseTestCase
{
    /**
     * @param array<float> $expected
     */
    #[DataProvider('nativeSequenceProvider')]
    public function testClosedOpenMatchesNativeSequence(float $max, array $expected): void
    {
        $randomizer = new Randomizer(new Mt19937(1024));
        $actual = [];

        for ($index = 0; $index < count($expected); $index++) {
            $actual[] = GammaSection::closedOpen($randomizer, $max);
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * Values captured from Randomizer::getFloat() on PHP 8.3.30.
     *
     * @return Generator<string, array{float, array<float>}>
     */
    public static function nativeSequenceProvider(): Generator
    {
        yield 'binary fraction' => [
            0.125,
            [
                0.081488511103914141,
                0.072114169781590992,
                0.0077766466854058136,
                0.0045041645698113697,
                0.0071696878037766087,
                0.10223849099473176,
                0.0086961307378339153,
                0.076955139391593805,
            ],
        ];

        yield 'fraction' => [
            0.123456789,
            [
                0.095376409103911791,
                0.072112531781587108,
                0.12197316868540342,
                0.023021331569808165,
                0.10439161680377591,
                0.10069391499472852,
                0.044187799737828798,
                0.044547708391593749,
            ],
        ];

        yield 'integer' => [
            1.0,
            [
                0.65190808883131313,
                0.57691335825272794,
                0.062213173483246509,
                0.036033316558490958,
                0.05735750243021287,
                0.81790792795785405,
                0.069569045902671323,
                0.61564111513275044,
            ],
        ];

        yield 'large fraction' => [
            1234.56789,
            [
                243.14092592657835,
                209.63987770166796,
                414.2108792937388,
                383.10742231185645,
                941.95719497709047,
                51.744426457752752,
                271.6272360087778,
                228.14126379187405,
            ],
        ];

        yield 'minimum normal' => [
            PHP_FLOAT_MIN,
            [
                6.760134347086892e-309,
                3.422758056362881e-309,
                2.7685781194469043e-309,
                1.6035358141922581e-309,
                2.5524935849345986e-309,
                1.4147372398224234e-308,
                3.0959253079864298e-309,
                5.1462004450100907e-309,
            ],
        ];

        yield 'maximum' => [
            PHP_FLOAT_MAX,
            [
                1.1719306958530906e+308,
                1.0371131835410054e+308,
                1.1184019486865369e+307,
                6.47768458032827e+306,
                1.0311118835159098e+307,
                1.4703474670390562e+308,
                1.2506379621777495e+307,
                1.1067338062131219e+308,
            ],
        ];
    }
}
