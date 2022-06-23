# My Validation rules for Laravel
In this repository I will add validation rules that I use in my SaaS app. I think especially Swedish developers will find them handy.

## Requirements
- PHP 8.0|8.1+
- Laravel v9.0+

## Installation
```bash
composer require tanthammar/laravel-rules
```

## Rules
See the src/Rules folder.

## Documentation
There won't be much documentation written, this repository will grow as I add items.
Hopefully the source code contains enough hints to use the components.

They are all used in the same way, following Laravel conventions. 

Example:
```php
use Tanthammar\LaravelRules\Rules\PhoneNumber;
use Tanthammar\LaravelRules\Rules\PersonNummer;

SomeField::make('phone')
    ->rules([ new PhoneNumber ])
SomeField::make('person_nummer')
    ->rules([ new PersonNummer ])
```




