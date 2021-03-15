# Validator

Describe schema of your data and then parse input data using described schema.

## Getting started

```php
<?php

use sbovyrin\Validator;

Validator::parse(SCHEMA)($input);
```

Where:
- `SCHEMA` describes rules for input parsing
- `$input` some data from one of app's forms


### Schema possible format

```php
<?php

SCHEMA = [
  'name' => ['type' => 'text', 'required' => true, 'max' => 64],
  'email' => ['type' => 'email', 'required' => true, 'max' => 128],
  'age' => ['type' => 'number', 'required' => false, 'min' => 18, 'max' => 100],
  'active' => ['type' => 'boolean', 'required' => true],
  'country' => [
    'type' => 'text',
    'required' => true,
    'in' => ['USA', 'Canada']
  ],
  // also valid empty rules
  'attribute' => []
];
```

- type: `text`|`number`|`boolean`|`email`
- required: `true`|`false`
- min|max: applies for number or length of string
- in: list of possible values
