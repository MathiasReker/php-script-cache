<?php
/**
 * This file is part of the php-script-cache package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpScriptCache\Service;

interface ScriptCacheServiceInterface
{
    /**
     * @param string[] $script
     */
    public function add(array $script): self;

    public function doMinify(bool $minify = false): self;

    public function setPath(string $path): self;

    public function build(): void;

    public function fetch(): string;
}
