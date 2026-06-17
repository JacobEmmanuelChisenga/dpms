<?php

namespace App\Rules;

use App\Support\ZambianNrc as ZambianNrcSupport;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ZambianNrc implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! ZambianNrcSupport::isValid($value)) {
            $fail(__('Enter a valid Zambian NRC in the format :format.', [
                'format' => ZambianNrcSupport::DISPLAY_HINT,
            ]));
        }
    }
}
