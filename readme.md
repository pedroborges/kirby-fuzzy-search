# Kirby Fuse Search (In Development) [![Release](https://img.shields.io/github/release/pedroborges/kirby-fuse-search.svg)](https://github.com/pedroborges/kirby-fuse-search/releases) [![Issues](https://img.shields.io/github/issues/pedroborges/kirby-fuse-search.svg)](https://github.com/pedroborges/kirby-fuse-search/issues)

Fuzzy-search plugin for Kirby based on the [Bitap](https://en.wikipedia.org/wiki/Bitap_algorithm) algorithm.

This is plugin is built on top of [Fuse](https://github.com/Loilo/Fuse), the PHP port of the awesome [Fuse.js](https://github.com/krisk/fuse) project.

## Basic Usage
If you are already using Kirby built-in `search` method, replacing it with Fuse Search is as easy as renaming the method on a page collection:

```diff
$query    = get('q');
$articles = page('blog')
    ->children()
    ->visible()
-   ->search($query, 'title|text');
+   ->fuseSearch($query, 'title|text');
```

Other than that, Fuse Search options are different from the ones of Kirby `search` method. **Soon** all available options will be documented here.

With Fuse Search you can also search through [custom page methods](https://getkirby.com/docs/developer-guide/objects/page) or [page models](https://getkirby.com/docs/developer-guide/advanced/models). You only need to include method name in the `fuseSearch` second parameter.

```php
page::$methods['authorFullName'] = function($page) {
    $user = site()->user($page->author()->value());

    return $user->firstname().' '.$user->lastname();
};
```

```php
$query    = get('q');
$articles = page('blog')
    ->children()
    ->visible()
    ->fuseSearch($query, 'title|text|authorFullName');
```

### Searching through structured fields
Fuse Search also comes a handy field method that lets you perform a search on a page field that contains a set of data.

```php
$result = page('faq')
    ->topics()
    ->fuseSearch($query, 'question|answer');
```

The `$result` will be a `Field` object and not just a simple array. That way you can chain any `Field` method, such as `toStructure`, `yaml`, or `isEmpty`, after searching for a term.

```php
$result = page('contact')
    ->addresses()
    ->fuseSearch($query, 'city')
    ->toStructure();
```

### Searching through arrays
You also can use the `fuseSearch` function to search through any associative array.

```php
$countries = [
    ['name' => 'Australia'],
    ['name' => 'Brazil'],
    ['name' => 'Canada'],
    ['name' => 'France'],
    ['name' => 'Germany'],
    ['name' => 'Portugal'],
    ['name' => 'United Kingdom'],
    ['name' => 'United States']
];

$results = fuseSearch($countries, 'Brasil', 'name');
```

## Option
These are the global options available for now. Check out the [Fuse options](https://github.com/Loilo/Fuse#options) to learn more about them.

```php
c::get('fuse-search.includeScore', true);
c::get('fuse-search.shouldSort', true);
c::get('fuse-search.threshould', 0.6);
```

## Installation

### Requirements
- Kirby 2.3.2+
- PHP 5.6+

### Download
[Download the files](https://github.com/pedroborges/kirby-fuse-search/archive/master.zip) and place them inside `site/plugins/fuse-search`.

### Kirby CLI
Kirby's [command line interface](https://github.com/getkirby/cli) makes installing the Fuse Search plugin a breeze:

    $ kirby plugin:install pedroborges/kirby-fuse-search

Updating couldn't be any easier, simply run:

    $ kirby plugin:update pedroborges/kirby-fuse-search

### Git Submodule
You can add the Fuse Search plugin as a Git submodule.

    $ cd your/project/root
    $ git submodule add https://github.com/pedroborges/kirby-fuse-search.git site/plugins/fuse-search
    $ git submodule update --init --recursive
    $ git commit -am "Add Fuse Search plugin"

Updating is as easy as running a few commands.

    $ cd your/project/root
    $ git submodule foreach git checkout master
    $ git submodule foreach git pull
    $ git commit -am "Update submodules"
    $ git submodule update --init --recursive

## Change Log
All notable changes to this project will be documented at: <https://github.com/pedroborges/kirby-fuse-search/blob/master/changelog.md>

## License
Fuse Search plugin is open-sourced software licensed under the [MIT license](http://www.opensource.org/licenses/mit-license.php).

Copyright © 2017 Pedro Borges <oi@pedroborg.es>
