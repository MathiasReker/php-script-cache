<?php
/**
 * This file is part of the php-script-cache package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpScriptCache\Model;

final class ScriptCache
{
    private array $script = [];

    private string $outputPath;

    private bool $minify = false;

    public function add(array $script): self
    {
        $this->script[] = $script;

        return $this;
    }

    public function doMinify(bool $minify): self
    {
        $this->minify = $minify;

        return $this;
    }

    public function isMinify(): bool
    {
        return $this->minify;
    }

    public function getOutputPath(): string
    {
        return $this->outputPath;
    }

    public function setOutputPath(string $path): void
    {
        $this->outputPath = $path;
    }

    public function getScriptBundles(): array
    {
        return $this->script;
    }
}
