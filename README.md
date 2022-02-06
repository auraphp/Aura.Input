# Aura.Input

The Aura.Input package contains tools to describe and filter user inputs from an HTML
form, including sub-forms/sub-fieldsets, fieldset collections, an interface
for injecting custom filter systems, and CSRF protection. Note that this
package does not include output functionality, although the "hints" provided
by the `Form` object can be used with any presentation system to generate an
HTML form.

## Installation and Autoloading

This package is installable and PSR-4 autoloadable via Composer as
[aura/input][].

## Dependencies

This package requires PHP 7.2 or later; it has been tested on PHP 7.2 - 8.1. We recommend using the latest available version of PHP as a matter of
principle.

Aura library packages may sometimes depend on external interfaces, but never on
external implementations. This allows compliance with community standards
without compromising flexibility. For specifics, please examine the package
[composer.json][] file.

## Quality

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/auraphp/Aura.Input/badges/quality-score.png?b=4.x)](https://scrutinizer-ci.com/g/auraphp/Aura.Input/)
[![codecov](https://codecov.io/gh/auraphp/Aura.Input/branch/4.x/graph/badge.svg)](https://codecov.io/gh/auraphp/Aura.Input)
[![Continuous Integration](https://github.com/auraphp/Aura.Input/actions/workflows/continuous-integration.yml/badge.svg?branch=4.x)](https://github.com/auraphp/Aura.Input/actions/workflows/continuous-integration.yml)

This project adheres to [Semantic Versioning](http://semver.org/).

To run the unit tests at the command line, issue `composer install` and then
`phpunit` at the package root. This requires [Composer][] to be available as
`composer`, and [PHPUnit][] to be available as `phpunit`.

This package attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

## Community

To ask questions, provide feedback, or otherwise communicate with other Aura
users, please join our [Google Group][], follow [@auraphp][], or chat with us
on Freenode in the #auraphp channel.

## Documentation

This package is fully documented [here](./docs/index.md).

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[Composer]: http://getcomposer.org/
[PHPUnit]: http://phpunit.de/
[Google Group]: http://groups.google.com/group/auraphp
[@auraphp]: http://twitter.com/auraphp
[download a release]: https://github.com/auraphp/Aura.Input/releases
[aura/input]: https://packagist.org/packages/aura/input
[composer.json]: ./composer.json
