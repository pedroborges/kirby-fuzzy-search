# Kirby Fuzzy Search (Beta) [![Release](https://img.shields.io/github/release/pedroborges/kirby-fuzzy-search.svg)](https://github.com/pedroborges/kirby-fuzzy-search/releases) [![Issues](https://img.shields.io/github/issues/pedroborges/kirby-fuzzy-search.svg)](https://github.com/pedroborges/kirby-fuzzy-search/issues)

Fuzzy-search plugin for Kirby. Looking for approximate matches of search queries in your content has never been this easy.

This is plugin is built on top of the [fuzzget](https://github.com/kevinfiol/fuzzget) PHP library.

## Basic Usage
If you are already using the Kirby built-in `search` method, replacing it with Fuzzy Search is just a matter of renaming a method on a pages collection:

```diff
$query    = get('q');
$articles = page('blog')
    ->children()
    ->visible()
-   ->search($query, 'title|text');
+   ->fuzzySearch($query, 'title|text');
```

Fuzzy Search **is not** compatible with any of the other options available on the Kirby `search` method.

With Fuzzy Search you can also search through [custom page methods](https://getkirby.com/docs/developer-guide/objects/page) or [page models](https://getkirby.com/docs/developer-guide/advanced/models). You only need to include the method name in the `fuzzySearch` last parameter.

```php
// site/plugins/methods.php
page::$methods['authorName'] = function($page) {
    $author = $page->author()->value();

    if ($user = site()->user($author)) {
        return $user->firstname().' '.$user->lastname();
    }
};
```

```php
$query    = get('q');
$articles = page('blog')
    ->children()
    ->visible()
    ->fuzzySearch($query, 'title|text|authorName');
```

### Searching through structured fields
Fuzzy Search ships with a handy field method that allows you to search on page fields that contains set of data, such as [structured fields](https://getkirby.com/docs/cookbook/structured-field-content).

```php
$result = page('faq')
    ->topics()
    ->fuzzySearch($query, 'question|answer');
```

The `$result` will also be a `Field` object and not just a simple array. That way you are free to chain any `Field` method, such as `toStructure`, `yaml`, or `isEmpty`, after doing a search.

```php
$result = page('contact')
    ->addresses()
    ->fuzzySearch($query, 'city')
    ->toStructure();
```

### Searching through arrays
You also can use the `fuzzySearch` function to search through an array of associative arrays.

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

$results = fuzzySearch($countries, 'Brasil');
```

## Advance Usage
If you leave the last parameter out, Fuzzy Search will search through all keys (fields) in the provided data:

```php
site()->index()->fuzzySearch($query);
```

It's the same as using the `*` wildcard.

```php
site()->index()->fuzzySearch($query, '*');
```

Fuzzy Search is very flexible when it comes to choosing which fields it should look for matches. Check out the other options:

### Include
If you want to search for a given term only in the `title` and `text` fields, just pass their names in the last parameter separated by `|`:

```php
site()->index()->fuzzySearch($query, 'title|text');
```

That is syntax sugar for:

```php
site()->index()->fuzzySearch($query, [
    'include' => ['title', 'text']
]);
```

### Ignore
Of course you can also list fields you do not want to search through:

```php
site()->index()->fuzzySearch($query, '-author|-date');
```

The above is the same as doing:

```php
site()->index()->fuzzySearch($query, [
    'ignore' => ['author', 'date']
]);
```

In this example, all fields will be considered in the search except for `author` and `date`.

If you need to include a custom page method or page model method, you can combine it with the wildcard and ignore syntax.

```php
site()->index()->fuzzySearch($query, '*|authorName|-date');
```

The above will include all fields but `date` along with `$page->authorName()`, in case it's a custom page method or page model method.

## Installation

### Requirements
- Kirby 2.3.2+
- PHP 7.0+

### Download
[Download the files](https://github.com/pedroborges/kirby-fuzzy-search/archive/master.zip) and place them inside `site/plugins/fuzzy-search`.

### Kirby CLI
Kirby's [command line interface](https://github.com/getkirby/cli) makes installing the Fuzzy Search plugin a breeze:

    $ kirby plugin:install pedroborges/kirby-fuzzy-search

Updating couldn't be any easier, simply run:

    $ kirby plugin:update pedroborges/kirby-fuzzy-search

### Git Submodule
You can add the Fuzzy Search plugin as a Git submodule.

    $ cd your/project/root
    $ git submodule add https://github.com/pedroborges/kirby-fuzzy-search.git site/plugins/fuzzy-search
    $ git submodule update --init --recursive
    $ git commit -am "Add Fuzzy Search plugin"

Updating is as easy as running a few commands.

    $ cd your/project/root
    $ git submodule foreach git checkout master
    $ git submodule foreach git pull
    $ git commit -am "Update submodules"
    $ git submodule update --init --recursive

## Change Log
All notable changes to this project will be documented at: <https://github.com/pedroborges/kirby-fuzzy-search/blob/master/changelog.md>

## License
Fuzzy Search plugin is open-sourced software licensed under the [MIT license](http://www.opensource.org/licenses/mit-license.php).

Copyright © 2017 Pedro Borges <oi@pedroborg.es>
