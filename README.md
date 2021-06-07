# Validator

Describe rules of your data and then parse input data using described your rules.

## Getting started (WIP)

```php
<?php

use sbovyrin\Validator;

// $rules looks like:
[
    'attr1': ['type' => 'text', 'required' => true, 'max' => 16],
    'attr2': ['type' => 'number', 'required' => true, 'max' => 100, 'min' => 1],
    'attr3': ['type' => 'boolean', 'required' => false],
    'attr4': ['type' => 'number', 'required' => true, 'in' => [1,2,3,4]],
    'attr5': ['type' => 'number', 'required' => true, 'pattern' => '[0-5]']
]

// $data looks like:
[
    'attr1' => 'Hello',
    'attr2' => 5,
    'attr3' => true,
    'attr4' => 3,
    'attr5' => 2
]

$validateDataByTheRules = Validator::parse($rules);
$validatedData = $validateDataByTheRules($data);
// or
$validatedData = Validator::parse($rules)($data);
```
