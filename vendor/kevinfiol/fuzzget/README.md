fuzzget
===

A fuzzy search library for PHP. I made this with intentions to use with an autocomplete library.

It implements [Jaro-Winkler Distance](https://en.wikipedia.org/wiki/Jaro%E2%80%93Winkler_distance), [Levenshtein Distance](https://en.wikipedia.org/wiki/Levenshtein_distance), and [Longest Common Substring](https://en.wikipedia.org/wiki/Longest_common_substring_problem) for its different search modes.

## Installation
>PHP 7.0 is required.

Install with [Composer](https://getcomposer.org/):
```bash
composer require kevinfiol/fuzzget
```

## Usage

Include the `Fuzz` class:
```php
use kevinfiol\fuzzget\Fuzz;
```

Initialize a `Fuzz` instance. The constructor requires 4 parameters:
```php
$fuzz = new Fuzz($source, 10, 1, true);
```
type | name | description
--- | --- | ---
array | $source | Array of associative arrays
int | $maxResults | Max number of results to retrieve
int | $searchMode |0 for Levenshtein, 1 for Jaro-Winkler
bool | $useLCS | Switch to factor in LCS for scoring

### Example
```php
$games = [
    [
        'name' => 'Terranigma',
        'date' => 'October 19th, 1995',
        'console' => 'Super Nintendo Entertainment System'
    ],
    [
        'name' => 'The Legend of Zelda: A Link Between Worlds',
        'date' => 'November 21st, 2013',
        'console' => 'Nintendo 3DS'    
    ],
    [
        'name' => 'Shovel Knight: Treasure Trove',
        'date' => 'March 2nd, 2017',
        'console' => 'Microsoft Windows'  
    ],
    [
        'name' => 'The Legend of Zelda: A Link to the Past',
        'date' => 'November 20th, 1991',
        'console' => 'Super Nintendo Entertainment System'  
    ],
    [
        'name' => 'Chrono Trigger',
        'date' => 'March 11th, 1995',
        'console' => 'Super Nintendo Entertainment System'  
    ]
];
```

Using Jaro-Winkler with LCS:
```php
$fuzz = new Fuzz($games, 3, 1, true);

// Minimum LCS set to 4:
$res = $fuzz->search('nintendo', 4);
```

Results are returned in descending order according to their score:
```
(
    [0] => Array
        (
            [name] => The Legend of Zelda: A Link Between Worlds
            [date] => November 21st, 2013
            [console] => Nintendo 3DS
        )

    [1] => Array
        (
            [name] => The Legend of Zelda: A Link to the Past
            [date] => November 20th, 1991
            [console] => Super Nintendo Entertainment System
        )

    [2] => Array
        (
            [name] => Terranigma
            [date] => October 19th, 1995
            [console] => Super Nintendo Entertainment System
        )
)
```