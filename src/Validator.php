<?php

declare(strict_types=1);

namespace sbovyrin;


class Validator
{
    private static function required($val, bool $isRequired): bool
    {
        if ($isRequired) return !is_null($val);
        return true;
    }

    private static function type($val, string $type): bool
    {
        switch ($type) {
            case 'file':
            case 'email':
            case 'text': return is_string($val);
            case 'number': return is_numeric($val);
            case 'boolean': return !is_null($val) && ($val == 1 || $val == 0);
        }

        return false;
    }

    private static function min($val, int $min): bool
    {
        switch (true) {
            case self::type($val, 'email'):
            case self::type($val, 'file'):
            case self::type($val, 'text'): return strlen($val) >= $min;
            case self::type($val, 'number'): return $val >= $min;
        }

        return false;
    }

    private static function max($val, int $max): bool
    {
        switch (true) {
            case self::type($val, 'email'):
            case self::type($val, 'file'):
            case self::type($val, 'text'): return strlen($val) <= $max;
            case self::type($val, 'number'): return $val <= $max;
        }

        return false;
    }

    private static function in($val, array $vals): bool
    {
        return in_array($val, $vals);
    }

    private static function pattern($val, string $pattern): bool
    {
        return (bool) preg_match("/{$pattern}/", $val);
    }

    /**
     * Parse $data structure using $rules
     *
     * @Example
     * $rules looks like:
     * [
     *      'attr1': ['type' => 'text', 'required' => true, 'max' => 16],
     *      'attr2': ['type' => 'number', 'required' => true, 'max' => 100, 'min' => 1],
     *      'attr3': ['type' => 'boolean', 'required' => false],
     *      'attr4': ['type' => 'number', 'required' => true, 'in' => [1,2,3,4]],
     *      'attr5': ['type' => 'number', 'required' => true, 'pattern' => '[0-5]']
     * ]
     *
     * $data looks like:
     * [
     *      'attr1' => 'Hello',
     *      'attr2' => 5,
     *      'attr3' => true,
     *      'attr4' => 3,
     *      'attr5' => 2
     * ]
     *
     * $validateDataByTheRules = Validator::parse($rules);
     * $validatedData = $validateDataByTheRules($data);
     * or
     * $validatedData = Validator::parse($rules)($data);
     */
    static function parse(array $rules, array $dflt = []): callable
    {
        $_parse = static fn ($v, $_rules): array =>
            empty($_rules)
                ? [[$v, []]]
                : array_map(
                    static fn ($p, $x) => self::$p($v, $x)
                        ? [$v, []]
                        : [$v, [$p => $x]],
                    array_keys($_rules),
                    $_rules
                );

        $_output = static fn (array $res): array => [
            'val' => $res[0][0],
            'errs' => array_merge(...array_column($res, 1))
        ];

        return static fn ($data):array =>
            array_reduce(
                array_keys($rules),
                static fn ($a, $b) => array_merge(
                    $a,
                    [
                        $b => $_output(
                            $_parse($data[$b] ?? $dflt[$b] ?? null, $rules[$b])
                        )
                    ]
                ),
                []
            );
    }
}
