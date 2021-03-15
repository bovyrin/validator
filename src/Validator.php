<?php declare(strict_types=1);

namespace sbovyrin;


class Validator
{
    static function rule(string $name): callable
    {
        return [
            'required' => fn ($v, array $rules) => 
                !is_null($v) || !$rules['required'],
            'type' => function ($v, array $rules) {
                switch ($rules['type']) {
                    case 'text':
                        return is_string($v);
                    case 'number':
                        return is_numeric($v);
                    case 'boolean':
                        return $v == 1 || $v == 0;
                    case 'email':
                        return is_string($v);
                }
            },
            'min' => function ($v, array $rules) {
                switch ($rules['type']) {
                    case 'text':
                        return !is_null($v) && strlen($v) >= $rules['min'];
                    case 'email':
                        return !is_null($v) && strlen($v) >= $rules['min'];
                }
            },
            'max' => function ($v, array $rules) {
                switch ($rules['type']) {
                    case 'text':
                        return !is_null($v) && strlen($v) <= $rules['max'];
                    case 'email':
                        return !is_null($v) && strlen($v) <= $rules['max'];
                }
            },
            'in' => fn ($v, array $rules) => in_array($v, $rules['in']),
            'pattern' => fn ($v, array $rules) => 
                (bool) !is_null($v) && preg_match("/{$rules['pattern']}/", $v)
        ][$name];
    }

    // TODO: need refactoring
    static function parse(array $rules): callable
    {
        return fn (array $data): array => array_reduce(
            array_keys($rules),
            function ($acc, $attr) use ($data, $rules) {

                // if not required skip parsing
                if (!isset($data[$attr]) && !($rules[$attr]['required'] ?? false)) {
                    return $acc;
                }

                $errs = array_reduce(
                    array_keys($rules[$attr]),
                    function ($_errs, string $name) use ($attr, $data, $rules) {
                        return array_merge(
                            $_errs,
                            self::rule($name)($data[$attr] ?? null, $rules[$attr])
                                ? []
                                : [$name => $rules[$attr][$name]]
                        );
                    },
                    []
                );

                if (empty($errs)) $acc['vals'][$attr] = $data[$attr];
                else $acc['errs'] = array_merge(
                    $acc['errs'] ?? [],
                    [$attr => $errs]
                );

                return $acc;
            },
            []
        );
    }
}
