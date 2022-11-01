<?php
/**
 * This file is part of the php-script-cache package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpScriptCache\Tests\Unit;

use MathiasReker\PhpScriptCache\ScriptCache;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \ScriptCacheService
 *
 * @small
 */
final class ScriptCacheTest extends TestCase
{
    private const ROOT = __DIR__ . '/assets/js';

    public function testSimple(): void
    {
        (new ScriptCache())
            ->setPath(self::ROOT)
            ->doMinify()
            ->add([
                'src' => [__DIR__ . '/stub/test.js'],
            ])
            ->build();

        $actual = (new ScriptCache())
            ->setPath(__DIR__ . '/assets/js')
            ->doMinify()
            ->add([
                'src' => [__DIR__ . '/stub/test.js'],
                'id' => 'test',
                'defer' => '',
            ])
            ->fetch();

        $expected = '<script src="' . self::ROOT . '/c5a80e42.js?v=ec25a610" id="test" defer></script>';

        self::assertSame($expected, $actual);
    }

    public function testUpdateParam(): void
    {
        // run cron first time
        (new ScriptCache())
            ->setPath(self::ROOT)
            ->doMinify()
            ->add([
                'src' => [__DIR__ . '/stub/test.js'],
            ])
            ->build();

        // update script
        file_put_contents(__DIR__ . '/stub/test.js', 'const test = 1;');

        // run cron again
        (new ScriptCache())
            ->setPath(self::ROOT)
            ->doMinify()
            ->add([
                'src' => [__DIR__ . '/stub/test.js'],
            ])
            ->build();

        $actual = (new ScriptCache())
            ->setPath(__DIR__ . '/assets/js')
            ->add([
                'src' => [__DIR__ . '/stub/test.js'],
                'id' => 'test',
                'defer' => '',
            ])
            ->fetch();

        $expected = '<script src="' . self::ROOT . '/c5a80e42.js?v=539e4fd1" id="test" defer></script>';

        self::assertSame($expected, $actual);
    }

    public function testBundle(): void
    {
        (new ScriptCache())
            ->setPath(self::ROOT)
            ->doMinify()
            ->add([
                'src' => [__DIR__ . '/stub/test.js', __DIR__ . '/stub/test2.js'],
            ])
            ->build();

        $actual = (new ScriptCache())
            ->setPath(__DIR__ . '/assets/js')
            ->add([
                'src' => [__DIR__ . '/stub/test.js', __DIR__ . '/stub/test2.js'],
                'id' => 'test',
                'defer' => '',
            ])
            ->fetch();

        $expected = '<script src="' . self::ROOT . '/570db81a.js?v=8a112094" id="test" defer></script>';

        self::assertSame($expected, $actual);
    }

    protected function setUp(): void
    {
        $files = [
            __DIR__ . '/stub/test.js' => 'let x = 1;',
            __DIR__ . '/stub/test2.js' => 'let y = 1;',
        ];

        if (!file_exists(__DIR__ . '/stub')) {
            mkdir(__DIR__ . '/stub', 0755, true);
        }

        foreach ($files as $file => $content) {
            file_put_contents($file, $content);
        }

        if (!file_exists(self::ROOT)) {
            mkdir(self::ROOT, 0755, true);
        }
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob(self::ROOT . '/*.*'));
        rmdir(self::ROOT);
    }
}
