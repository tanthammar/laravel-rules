# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel package that provides custom validation rules, factory helpers, and services primarily focused on Swedish localization. The package includes validation rules for Swedish business/personal numbers, phone numbers, bank accounts, and VAT IDs.

## Architecture

### Core Components

- **Rules**: Custom Laravel validation rules in `src/Rules/`
- **Factories**: Faker providers for generating test data in `src/Factories/`
- **Services**: Business logic services in `src/Services/`
- **Helpers**: Legacy helper functions in `src/Helpers/` and `src/RuleHelpers.php`

### Key Dependencies

- `personnummer/personnummer`: Swedish personal number validation
- `organisationsnummer/organisationsnummer`: Swedish organization number validation
- `brick/phonenumber`: International phone number validation
- `byrokrat/banking`: Swedish banking system validation
- `mpociot/vat-calculator`: EU VAT validation services
- `tanthammar/laravel-extras`: Custom Laravel utilities

### Service Provider

The `LaravelRulesServiceProvider` registers custom validation rules and handles translations:
- Extends basic Laravel validators with Swedish-specific rules
- Registers custom text validation rules: `alpha_space`, `alpha_num_space`, `alpha_dash_space`, etc.
- Handles internationalization for Swedish (`sv`) and English (`en`)

## Rule Architecture

All validation rules follow Laravel's Rule contract and implement both legacy and modern validation patterns:
- Legacy: `passes()` method with `message()` method
- Modern: `validate()` method with closure-based failure handling

### Key Validation Rules

- **PersonNummer**: Swedish personal identification numbers
- **OrgNummer**: Swedish organization numbers  
- **VatNumber**: EU VAT ID validation with service availability handling
- **PhoneNumber**: International phone numbers with automatic "+" prefix handling
- **BankGiro/PlusGiro**: Swedish payment system validation
- **BankKonto**: Swedish bank account validation

## Development Workflow

### Installation
```bash
composer require tanthammar/laravel-rules
```

### Usage Pattern
```php
use TantHammar\LaravelRules\Rules\PhoneNumber;
use TantHammar\LaravelRules\Rules\PersonNummer;

$field->rules([new PhoneNumber]);
$field->rules([new PersonNummer]);
```

### Testing Data Generation
Use factory classes for generating Swedish test data:
```php
FakePhoneNumber::make(international: true)
FakePhoneNumber::gdprSafe() // GDPR-compliant test numbers
```

## Services and Deprecation

The package has migrated from static helper methods to dedicated service classes:
- `RuleHelpers::getBusinessNameFromVatID()` → `BusinessNameFromVatID::lookup()`
- `RuleHelpers::getVATDetailsFromVatID()` → `VatDetailsFromVatID::lookup()`
- `RuleHelpers::check_business_type()` → `BusinessTypeFromNr::make()`

## Internationalization

Translations are stored in `resources/lang/` with support for Swedish and English. Error messages are prefixed with `laravel-rules::messages.` for proper namespacing.

## Development Commands

This package does not include test suites, linting, or build scripts. Development is typically done by:
- Installing the package in a Laravel project via `composer require tanthammar/laravel-rules`
- Testing validation rules directly in Laravel applications
- Manual testing of factory helpers and services

## Development Notes

- The package supports PHP 8.1+ and Laravel 10.0+
- External API dependencies (VAT validation) include proper exception handling for service unavailability
- Phone number validation automatically handles missing "+" prefix
- All number inputs are cleaned using `CleanNumber::make()` from the extras package