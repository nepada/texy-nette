Texy integration for Nette
==========================

[![Build Status](https://github.com/nepada/texy-nette/workflows/CI/badge.svg)](https://github.com/nepada/texy-nette/actions?query=workflow%3ACI+branch%3Amaster)
[![Coverage Status](https://coveralls.io/repos/github/nepada/texy-nette/badge.svg?branch=master)](https://coveralls.io/github/nepada/texy-nette?branch=master)
[![Downloads this Month](https://img.shields.io/packagist/dm/nepada/texy-nette.svg)](https://packagist.org/packages/nepada/texy-nette)
[![Latest stable](https://img.shields.io/packagist/v/nepada/texy-nette.svg)](https://packagist.org/packages/nepada/texy-nette)


Installation
------------

Via Composer:

```sh
$ composer require nepada/texy-nette
```

Register the extension in `config.neon`:

```yaml
extensions:
    texy: Nepada\Bridges\TexyDI\TexyExtension
```


Usage
-----

### Configuration

This extension contains simple Texy factory that only creates new instance of `Texy\Texy`.

Usually you will want to define your own factory by implementing `Nepada\Texy\TexyFactory`, often more than one.

```yaml
texy:
    factories:
        foo: @fooTexyFactory
        bar: @barTexyFactory

    defaultMode: foo
```

This example adds two custom factories. Note the names `foo` and `bar` - we call these Texy "modes". In different parts of your application you might need to use different mode (i.e. differently configured instance of Texy).

### In templates

Latte templates come with a couple of filters that you can use:

- `|texy:customMode` calls `$texyMultiplier->processBlock()`
- `|texyLine:customMode` calls `$texyMultiplier->processLine()`
- `|texyTypo:customMode` calls `$texyMultiplier->processTypo()`

You can pass the name of custom mode as an argument to each of the filters.

### In presenters and other controls

The preferred way is to inject `Nepada\Texy\TexyMultiplier` instance wherever you need it, and either use it directly or pull out desired Texy instance, e.g:

 ```php
$multiplier->processBlock($text, 'myMode');
$texy = $multiplier->getTexy('myMode');
 ```
