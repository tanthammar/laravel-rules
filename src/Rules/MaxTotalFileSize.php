<?php

namespace App\Rules;

namespace TantHammar\LaravelRules\Rules;

use Illuminate\Contracts\Validation\Rule;
use Storage;

/**
 * $value must be an array with files
 * Default max is 5 MB (5000Kb)
 */
class MaxTotalFileSize implements Rule
{
    public function __construct(
        public int $maxKb = 5000
    ) {
    }

    public function passes($attribute, $value): bool
    {
        if (! is_array($value)) {
            return false;
        }

        if (blank($value)) {
            return true;
        }

        $total_size = array_reduce($value, static function ($sum, $file) {
            // each item is UploadedFile Object
            $sum += Storage::size((Storage::path($file)));

            return $sum;
        });

        return $total_size <= ($this->maxKb * 1024);
    }

    public function message(): string
    {
        return __('laravel-rules::messages.total-file-sizes', ['kb' => $this->maxKb]);
    }

    //Laravel 10
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
