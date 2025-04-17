<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SalePriceLessThanRegularPrice implements Rule
{
    protected $regularPrice;

    public function __construct($regularPrice)
    {
        $this->regularPrice = $regularPrice;
    }

    public function passes($attribute, $value)
    {
        // If sale_price is null, no need to check
        if (is_null($value)) {
            return true;
        }

        return $value < $this->regularPrice;
    }

    public function message()
    {
        return 'The sale price must be less than the regular price.';
    }
}
