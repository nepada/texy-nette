Texy integration for Nette
==========================

[![Build Status](https://travis-ci.org/nepada/texy-nette.svg?branch=master)](https://travis-ci.org/nepada/texy-nette)
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

This extension relies on [nepada/template-factory](https://github.com/nepada/template-factory) - make sure you've enabled it as well.


Usage
-----

### Configuration

This extension contains simple Texy factory that only creates new instance of `Texy\Texy` and sets output mode to `HTML5`.

Usually you will want to define your own factory by implementing `Nepada\Texy\ITexyFactory`, often more than one.

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

- `|texy` calls `$texy->process()`
- `|texyLine` calls `$texy->processLine()`
- `|texyTypo` calls `$texy->processType()`

If you need to switch between different Texy modes, wrap your code into macro `{texyMode modeName}...{/texyMode}`. A good practice is to always use this macro and not rely on the default mode.

### In presenters and other controls

The preferred way is to inject `Nepada\Texy\TexyMultiplier` instance wherever you need it, and either use it directly or pull out desired Texy instance, e.g:

 ```php
$texy = $multiplier->getTexy('myMode');
$texy->process(...);
 ```
