<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CompanyEmail implements Rule
{
    /**
     * Company domain to validate against.
     *
     * @var string
     */
    protected string $domain;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return optional(explode('@', $value))[1] === $this->domain;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.custom.company_email', ['attribute' => 'email']);
    }
}
