<?php
/**
 * This file is part of the php-script-cache package.
 * (c) Mathias Reker <github@reker.dk>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MathiasReker\PhpScriptCache\Service;

use JsonException;
use MathiasReker\PhpScriptCache\Exception\InvalidArgumentException;
use MathiasReker\PhpScriptCache\Model\ScriptCache;
use MatthiasMullie\Minify\JS as MinifyJs;

class ScriptCacheService implements ScriptCacheServiceInterface
{
    /**
     * @var int
     */
    private const CACHE_KEY_OFFSET = 8;

    /**
     * @var int
     */
    private const CACHE_VERSION_OFFSET = 8;

    private ScriptCache $scriptCache;

    public function __construct()
    {
        $this->scriptCache = new ScriptCache();
    }

    /**
     * @param string[] $script
     */
    public function add(array $script): self
    {
        $this->scriptCache->add($script);

        return $this;
    }

    public function doMinify(bool $minify = true): self
    {
        $this->scriptCache->doMinify($minify);

        return $this;
    }

    public function setPath(string $path): self
    {
        $this->scriptCache->setOutputPath(realpath($path));

        return $this;
    }

    /**
     * @throws \JsonException
     */
    public function fetch(): string
    {
        $result = '';

        foreach ($this->scriptCache->getScriptBundles() as $bundle) {
            if (!isset($bundle['src'])) {
                throw new InvalidArgumentException();
            }

            $src = mb_substr(sha1(json_encode(
                $bundle['src'],
                \JSON_THROW_ON_ERROR
            )), 0, self::CACHE_KEY_OFFSET);

            if (!isset($this->getMetaData()[$src])) {
                throw new InvalidArgumentException();
            }

            $bundle['src'] = sprintf(
                '%s/%s%s?v=%s',
                $this->scriptCache->getOutputPath(),
                $src,
                $this->getExtension(),
                $this->getMetaData()[$src]
            );

            array_walk($bundle, static function (&$value, $attribute): void {
                $value = sprintf('%s="%s"', $attribute, $value);
            });

            $result .= sprintf('<script %s></script>', implode(' ', $bundle));
        }

        return $result;
    }

    /**
     * @return string[]
     *
     * @throws JsonException
     */
    private function getMetaData(): array
    {
        $metaData = sprintf('%s/%s.json', $this->scriptCache->getOutputPath(), 'meta');

        return (array) json_decode(
            file_get_contents($metaData),
            true,
            512,
            \JSON_THROW_ON_ERROR
        );
    }

    private function getExtension(): string
    {
        return $this->scriptCache->isMinify()
            ? '.min.js'
            : '.js';
    }

    /**
     * @throws JsonException
     */
    public function build(): void
    {
        if (!file_exists($this->scriptCache->getOutputPath())) {
            mkdir($this->scriptCache->getOutputPath(), 0775, true);
        }

        array_map('unlink', glob(sprintf('%s/*.*', $this->scriptCache->getOutputPath())));

        $bundles = $this->scriptCache->getScriptBundles();

        $scripts = [];

        foreach ($bundles as $bundle) {
            if (!isset($bundle['src'])) {
                throw new InvalidArgumentException();
            }

            $cacheKey = mb_substr(sha1(json_encode(
                $bundle['src'],
                \JSON_THROW_ON_ERROR
            )), 0, self::CACHE_KEY_OFFSET);

            $scripts[$cacheKey][] = $bundle;
        }

        $metadata = [];

        foreach ($scripts as $script => $attributes) {
            $scriptPath = sprintf(
                '%s/%s%s',
                $this->scriptCache->getOutputPath(),
                $script,
                $this->getExtension()
            );

            $fp = fopen($scriptPath, 'a+');

            foreach ($attributes as $attribute) {
                foreach ($attribute['src'] as $src) {
                    $output = $this->scriptCache->isMinify()
                        ? (new MinifyJs(file_get_contents($src)))->minify()
                        : file_get_contents($src);

                    fwrite($fp, $output . \PHP_EOL);

                    $attribute['src'] = $scriptPath;
                }

                $metadata[] = $script;
            }

            fclose($fp);
        }

        $this->cacheMetadata($metadata);
    }

    /**
     * @param string[] $scripts
     *
     * @throws JsonException
     */
    private function cacheMetadata(array $scripts): void
    {
        $metaData = [];

        foreach ($scripts as $scriptName) {
            $scriptPath = sprintf(
                '%s/%s%s',
                $this->scriptCache->getOutputPath(),
                $scriptName,
                $this->getExtension()
            );
            $metaData[$scriptName] = mb_substr(sha1_file($scriptPath), 0, self::CACHE_VERSION_OFFSET);
        }

        $metaFilePath = sprintf('%s/%s.json', $this->scriptCache->getOutputPath(), 'meta');

        $fp = fopen($metaFilePath, 'a+');
        fwrite($fp, json_encode($metaData, \JSON_THROW_ON_ERROR));
        fclose($fp);
    }
}
