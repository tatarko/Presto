# Presto templating engine

Presto is simple and lightweight php templating engine based on the syntax of twig. It was built as part for `Opiner CMS` framework but become solo project that you can use as composer package in your own project.

## Requirements

Presto requires to run correctly:

- `PHP` version `5.3` or above

## Instalation

### Composer

Simply add a dependency on `tatarko/presto` to your project's `composer.json` file if you use [Composer](http://getcomposer.org) to manage the dependencies of your project. Here is a minimal example of a `composer.json` file that just defines a dependency on `Presto`:

```json
{
	"require": {
		"tatarko/presto": "~1.0"
	}
}
```

### Straight implementation

In case you don't use `Composer` as your dependency manager you are still able to use `Presto`. There are only two easy steps  to get `Presto` work.

1.  Download [presto.zip](https://github.com/tatarko/presto/archive/master.zip) and put extracted archive into your project's folder.
2. Add following code to your project's root php file (e.g. `index.php`) and remember to change `path/to/` according to relative location of downloaded `presto` folder:

```php
require_once 'path/to/source/__autoloader.php';
```

## Documentation

Please, see [Wiki](https://github.com/tatarko/presto/wiki) for online documentation.