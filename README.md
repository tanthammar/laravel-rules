# Laravel Rules & Validators
Custom validation rules, factory helpers, and services for Laravel applications, with a focus on Swedish localization.

## Requirements
- PHP 8.1+
- Laravel 10.0+

## Installation
```bash
composer require tanthammar/laravel-rules
```

## Validation Rules

### Swedish Personal & Organization Numbers
```php
use TantHammar\LaravelRules\Rules\PersonNummer;
use TantHammar\LaravelRules\Rules\OrgNummer;
use TantHammar\LaravelRules\Rules\PersonOrOrgNummer;

// Swedish personal identification numbers
$request->validate([
    'person_number' => [new PersonNummer]
]);

// Swedish organization numbers
$request->validate([
    'org_number' => [new OrgNummer]
]);

// Accept either personal or organization numbers
$request->validate([
    'number' => [new PersonOrOrgNummer]
]);
```

### Phone Numbers
```php
use TantHammar\LaravelRules\Rules\PhoneNumber;
use TantHammar\LaravelRules\Rules\MobileNumber;
use TantHammar\LaravelRules\Rules\FixedLineNumber;

// International phone numbers (handles missing + prefix automatically)
$request->validate([
    'phone' => [new PhoneNumber]
]);

// Mobile numbers only
$request->validate([
    'mobile' => [new MobileNumber]
]);

// Fixed line numbers only
$request->validate([
    'landline' => [new FixedLineNumber]
]);
```

### Swedish Banking
```php
use TantHammar\LaravelRules\Rules\BankKonto;
use TantHammar\LaravelRules\Rules\BankGiro;
use TantHammar\LaravelRules\Rules\PlusGiro;

// Swedish bank account numbers
$request->validate([
    'bank_account' => [new BankKonto]
]);

// BankGiro payment numbers
$request->validate([
    'bankgiro' => [new BankGiro]
]);

// PlusGiro payment numbers
$request->validate([
    'plusgiro' => [new PlusGiro]
]);
```

### VAT Number Validation

⚠️ **Important**: Choose the right VAT validation rule based on your needs:

#### VatNumberFormat (Recommended for most cases)
Only validates the format using regex - no external API calls.
```php
use TantHammar\LaravelRules\Rules\VatNumberFormat;

$request->validate([
    'vat_number' => [new VatNumberFormat]
]);
```

#### VatNumberAPI (Use with caution)
Validates against the VIES EU API but throws exceptions when service is unavailable.
```php
use TantHammar\LaravelRules\Rules\VatNumberAPI;
use Mpociot\VatCalculator\Exceptions\VATCheckUnavailableException;

try {
    $request->validate([
        'vat_number' => [new VatNumberAPI]
    ]);
} catch (VATCheckUnavailableException $e) {
    // Handle service unavailability
    // Service can be down for hours or days
    return back()->withErrors([
        'vat_number' => 'VAT validation service is temporarily unavailable. Please try again later.'
    ]);
}
```

#### VatNumberAPIWithFormatFallback (Balanced approach)
Tries API validation first, falls back to format validation if service is unavailable.
```php
use TantHammar\LaravelRules\Rules\VatNumberAPIWithFormatFallback;

$request->validate([
    'vat_number' => [new VatNumberAPIWithFormatFallback]
]);
```

### Geographic Coordinates
```php
use TantHammar\LaravelRules\Rules\Latitude;
use TantHammar\LaravelRules\Rules\Longitude;

$request->validate([
    'lat' => [new Latitude],
    'lng' => [new Longitude]
]);
```

### File Upload Validation
```php
use TantHammar\LaravelRules\Rules\MaxTotalFileSize;

// Limit total size of all uploaded files
$request->validate([
    'documents.*' => ['file'],
    'documents' => [new MaxTotalFileSize(5000)] // 5MB total
]);
```

## Factory Helpers

Generate realistic test data for Swedish formats:

```php
use TantHammar\LaravelRules\Factories\FakePhoneNumber;
use TantHammar\LaravelRules\Factories\FakeMobileNumber;
use TantHammar\LaravelRules\Factories\FakeBankgiro;
use TantHammar\LaravelRules\Factories\FakePlusgiro;

// Generate Swedish phone numbers
$phone = FakePhoneNumber::make(international: true);
$localPhone = FakePhoneNumber::make(international: false);

// GDPR-safe phone numbers (official test ranges)
$gdprPhone = FakePhoneNumber::gdprSafe();

// Mobile numbers
$mobile = FakeMobileNumber::make();

// Payment numbers
$bankgiro = FakeBankgiro::make();
$plusgiro = FakePlusgiro::make();
```

## Services

### VAT Information Lookup
```php
use TantHammar\LaravelRules\Services\BusinessNameFromVatID;
use TantHammar\LaravelRules\Services\VatDetailsFromVatID;

// Get business name from VAT ID
$businessName = BusinessNameFromVatID::lookup('SE556556567801');

// Get full VAT details
$details = VatDetailsFromVatID::lookup('SE556556567801');
```

### Business Type Detection
```php
use TantHammar\LaravelRules\Helpers\BusinessTypeFromNr;

// Determine if number is personal, business, or undefined
$type = BusinessTypeFromNr::make('199001011234'); // Returns: 'individual'
$type = BusinessTypeFromNr::make('556556567801'); // Returns: 'business'
$type = BusinessTypeFromNr::make('invalid');     // Returns: 'undefined'
```

## Legacy Helper Methods (Deprecated)

These methods are deprecated but still available for backwards compatibility:

```php
use TantHammar\LaravelRules\RuleHelpers;

// Use BusinessNameFromVatID::lookup() instead
$name = RuleHelpers::getBusinessNameFromVatID($vatId);

// Use VatDetailsFromVatID::lookup() instead  
$details = RuleHelpers::getVATDetailsFromVatID($vatId);

// Use BusinessTypeFromNr::make() instead
$type = RuleHelpers::check_business_type($number);
```




