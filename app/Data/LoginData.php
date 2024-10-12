<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class LoginData extends Data
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }

    // Add validation rules for your fields
    public static function rules(): array
    {
        return [
        'email' => ['required', 'email'],
        'password' => ['required', 'string', 'min:8'],
        ];
    }
}
