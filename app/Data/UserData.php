<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\StringType;

class UserData extends Data
{
    public function __construct(
        //        #[Required, StringType]
        //        public string $id,
        #[Required, StringType]
        public string $name,
        #[Required, Email]
        public string $email,
        #[Required, StringType]
        public string $password,
        #[Nullable, StringType]
        public ?string $email_verified_at,
        #[ArrayType]
        public array $roles = [],
    ) {
    }



}
