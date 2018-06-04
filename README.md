# Lumberjack QueryBuilder
![CI](https://travis-ci.org/Rareloop/lumberjack-querybuilder.svg?branch=master)
![Coveralls](https://coveralls.io/repos/github/Rareloop/lumberjack-querybuilder/badge.svg?branch=master)

Experimental QueryBuilder for Lumberjack Post objects.

## Install
```
composer require rareloop/lumberjack-querybuilder
```

Once installed, register the Service Provider in `config/app.php` within your theme:

```
'providers' => [
    ...

    Rareloop\Lumberjack\QueryBuilder\QueryBuilderServiceProvider::class,

    ...
],
```
