<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class PermissionData extends Data
{
    public function __construct(
        public ?string $ulid,
        public string $name,
        public string $guard_name,
    ) {
    }
}
