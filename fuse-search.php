<?php

/**
 * Kirby Fuse Search Plugin
 *
 * @version   1.0.0-alpha
 * @author    Pedro Borges <oi@pedroborg.es>
 * @copyright Pedro Borges <oi@pedroborg.es>
 * @link      https://github.com/pedroborges/kirby-fuse-search
 * @license   MIT
 */

// Load dependencies
require __DIR__.DS.'vendor'.DS.'autoload.php';

if (! function_exists('fuseSearch')) {
    /**
     * Perform fuzzy search on an array of data.
     *
     * @param  array   $data
     * @param  string  $query
     * @param  array   $options
     * @return array
     */
    function fuseSearch(array $data, string $query, $options = []) {
        if (is_string($options)) {
            $options = ['keys' => Str::split($options, '|')];
        }

        $fuse = new Fuse\Fuse($data, array_merge([
            'includeScore' => c::get('fuse-search.includeScore', true),
            'shouldSort' => c::get('fuse-search.shouldSort', true),
            'threshold' => c::get('fuse-search.threshould', 0.6)
        ], $options));

        $results = $fuse->search($query);

        return array_map(function($result) {
            if (c::get('fuse-search.includeScore', true)) {
                // Normalize data structure
                return array_merge(
                    $result['item'],
                    ['searchScore' => $result['score']]
                );
            }

            return $result;
        }, $results);
    }
}

$kirby->set('field::method', 'fuseSearch', function(Field $field, string $query, $options = []) {
    $results = fuseSearch($field->yaml(), $query, $options);

    return new Field(
        $field->page(),
        $field->key(),
        Yaml::encode($results)
    );
});

$kirby->set('pages::method', 'fuseSearch', function(Pages $pages, string $query, $options = []) {
    if (is_string($options)) {
        $options = [
            'keys' => Str::split($options, '|'),
            // Sorting will be done on the pages collection
            'shouldSort' => false
        ];
    }

    $data = $pages->contentToArray($options['keys']);

    $results = fuseSearch(
        array_values($data),
        $query,
        $options
    );

    return $pages->fromFuseSearch($results);
});

$kirby->set('pages::method', 'fromFuseSearch', function(Pages $pages, array $results) {
    $ids = [];

    foreach ($results as $result) {
        $ids[$result['id']] = $result;
    }

    $pages = $pages->filterBy('id', 'in', array_keys($ids));

    if (c::get('fuse-search.includeScore', true)) {
        $pages = $pages->map(function($page) use ($ids) {
            $page->content()->data['searchscore'] = new Field(
                $page,
                'searchscore',
                $ids[$page->id()]['searchScore']
            );

            return $page;
        });
    }

    if (c::get('fuse-search.shouldSort', true)) {
        $pages = $pages->sortBy('searchScore');
    }

    return $pages;
});

$kirby->set('pages::method', 'contentToArray', function(Pages $pages, array $fields = []) {
    return array_map(function($page) use ($fields) {
        $content = ['id' => $page->id()];

        if (count($fields)) {
            foreach ($fields as $field) {
                $content[$field] = (string) $page->{$field}();
            }

            return $content;
        }

        return array_merge(
            $content,
            $page->content()->toArray()
        );
    }, $pages->data);
});
