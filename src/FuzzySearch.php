<?php

namespace PedroBorges\FuzzySearch;

use InvalidArgumentException;
use Field;
use Pages;
use Str;
use kevinfiol\fuzzget\Fuzz;

class FuzzySearch
{
    protected $data = [];
    protected $included = [];
    protected $ignored = [];

    public function __construct($data, $fields = [])
    {
        $this->data = $data;
        $this->setFields($fields);
    }

    public function search(string $query)
    {
        $fuzz = new Fuzz($this->parsedData(), 10, 1, true);

        return $fuzz->search($query, 3);
    }

    protected function parsedData()
    {
        $data = $this->data;

        if (is_a($data, 'Collection')) {
            return $this->transformPages($data);
        }

        if (is_a($data, 'Field')) {
            return $this->transformArray($data->yaml());
        }

        if (! is_array($data)) {
            throw new InvalidArgumentException(
                'The $data provided must be an array, instance of Pages or Field objects.'
            );
        }

        return $this->transformArray($data);
    }

    protected function transformPages(Pages $pages)
    {
        $data = array_map(function($page) {
            $content = $page->content()->toArray();
            $content = $this->filterContent($content);

            // Fields not present in the content array
            // are considered computed fields and will be
            // treated as custom page method or model method
            $computed = array_diff(
                $this->included,
                $page->content()->fields()
            );

            if (count($computed)) {
                // Remove wildcard from computed fields
                if (isset($computed[0]) && $computed[0] === '*') {
                    unset($computed[0]);
                }

                foreach ($computed as $field) {
                    $content[$field] = (string) $page->{$field}();
                }
            }

            $content['id'] = $page->id();

            return $content;
        }, $pages->data);

        return array_values($data);
    }

    protected function transformArray(array $data)
    {
        return array_map(function($content) {
            return $this->filterContent($content);
        }, $data);
    }

    protected function filterContent(array $content)
    {
        // Do not filter content if wildcard
        // is passed as first field item
        if (count($this->included) && $this->included[0] !== '*') {
            $content = array_filter($content, function($field) {
                return in_array($field, $this->included);
            }, ARRAY_FILTER_USE_KEY);
        }

        if (count($this->ignored)) {
            $content = array_filter($content, function($field) {
                return ! in_array($field, $this->ignored);
            }, ARRAY_FILTER_USE_KEY);
        }

        return $content;
    }

    protected function setFields($fields)
    {
        if (is_string($fields)) {
            $fields = Str::split($fields, '|');
        }

        $this->included = isset($fields['include'])
            ? $fields['include']
            : $this->fieldsToInclude($fields);

        $this->ignored = isset($fields['ignore'])
            ? $fields['ignore']
            : $this->fieldsToIgnore($fields);
    }

    protected function fieldsToInclude(array $fields = [])
    {
        return array_filter($fields, function($field) {
            return ! is_array($field) && ! Str::startsWith($field, '-');
        });
    }

    protected function fieldsToIgnore(array $fields = [])
    {
        $fields = array_filter($fields, function($field) {
            return ! is_array($field) && Str::startsWith($field, '-');
        });

        // Remove dash and return ignored fields
        return array_map(function($field) {
            return substr($field, 1);
        }, $fields);
    }
}
