<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;

class UpdateUserData extends Data
{
    public function __construct(
        #[Required, StringType]
        public string $name,
        #[Required, Email]
        public string $email,
        #[Nullable, StringType]
        public ?string $password = null, // Cho phép null nếu không muốn thay đổi
        #[Nullable, StringType]
        public ?string $avatar = null
    ) {
    }

}
