<h1 align="center">PHP script cache</h1>

[![Packagist Version](https://img.shields.io/packagist/v/MathiasReker/php-script-cache.svg)](https://packagist.org/packages/MathiasReker/php-script-cache)
[![Packagist Downloads](https://img.shields.io/packagist/dt/MathiasReker/php-script-cache.svg?color=%23ff007f)](https://packagist.org/packages/MathiasReker/php-script-cache)
[![CI status](https://github.com/MathiasReker/php-script-cache/actions/workflows/ci.yml/badge.svg?branch=develop)](https://github.com/MathiasReker/php-script-cache/actions/workflows/ci.yml)
[![Contributors](https://img.shields.io/github/contributors/MathiasReker/php-script-cache.svg)](https://github.com/MathiasReker/php-script-cache/graphs/contributors)
[![Forks](https://img.shields.io/github/forks/MathiasReker/php-script-cache.svg)](https://github.com/MathiasReker/php-script-cache/network/members)
[![Stargazers](https://img.shields.io/github/stars/MathiasReker/php-script-cache.svg)](https://github.com/MathiasReker/php-script-cache/stargazers)
[![Issues](https://img.shields.io/github/issues/MathiasReker/php-script-cache.svg)](https://github.com/MathiasReker/php-script-cache/issues)
[![MIT License](https://img.shields.io/github/license/MathiasReker/php-script-cache.svg)](https://github.com/MathiasReker/php-script-cache/blob/develop/LICENSE.txt)

`php-script-cache` is a PHP library for caching external scripts locally.

> ✅ Cache
> ✅ Bundle
> ✅ Minify

### Versions & Dependencies

| Version | PHP  | Documentation |
|---------|------|---------------|
| ^1.0    | ^7.4 | current       |

### Requirements

- `PHP` >= 7.4
- php-extension `ext-json`

### Installation

Run:

```bash
composer require mathiasreker/php-script-cache
```

### Examples

Set up a cronjob to run the build.

```php
<?php

use MathiasReker\PhpScriptCache\ScriptCache;

require __DIR__ . '/vendor/autoload.php';

(new ScriptCache())
    ->setPath(__DIR__ . '/assets/js')
    ->doMinify()
    ->add([
        'src' => ['https://example.com/script.js'], // multiple items will get bundled
    ])
    ->add([
        'src' => ['https://example.com/script.js'],
    ])
    ->build();
```

Place this code inside your PHP. You can add attributes to the scripts.

```php
<?php

use MathiasReker\PhpScriptCache\ScriptCache;

require __DIR__ . '/vendor/autoload.php';

(new ScriptCache())
    ->setPath(__DIR__ . '/assets/js')
    ->doMinify()
    ->add([
        'src' => ['https://example.com/script.js'],
    ])
    ->add([
        'src' => ['https://example.com/script.js'],
        'id' => 'test',
        'defer' => '',
    ])
    ->fetch();

```

### Documentation

```php
$result = new ScriptCache();
```

`setPath` sets the path of the output folder for built files:

```php
$result->doMinify();
```

`doMinify` minifies the content and add `.min.js` as the extension instead for `.js`:

```php
$result->add(['src' => ['https://example.com/script.js']]);
```

`add` sets the attributes of the script. The `src` can be an array of several scripts.
All the scripts in the collection will get bundled. You can use any attributes

```php
$result->build();
```

`build` builds the assets.
```php
$result->fetch();
```

`fetch` returns a string of the script tags.

### Roadmap

See the [open issues](https://github.com/MathiasReker/php-script-cache/issues) for a complete list of proposed
features (and known issues).

### Contributing

If you have a suggestion to improve this, please fork the repo and create a pull request. You can also open an issue
with the tag "enhancement". Finally, don't forget to give the project a star! Thanks again!

#### Docker

If you are using docker, you can use the following command to get started:

```bash
docker-compose up -d
```

Next, access the container:

```bash
docker exec -it php-script-cache bash
```

Install dependencies:

```bash
composer install
```

#### Tools

PHP Coding Standards Fixer:

```bash
composer cs-fix
```

PHP Coding Standards Checker:

```bash
composer cs-check
```

PHP Stan:

```bash
composer phpstan
```

Unit tests:

```bash
composer test
```

### License

It is distributed under the MIT License. See `LICENSE` for more information.
