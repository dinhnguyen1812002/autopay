<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AgencyData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $websiteUrl = null,
        public ?UploadedFile $logo = null,
        public ?string $logoUrl = null,
        public ?string $supportEmail = null,
        public ?string $customDomain = null,
        public bool $isActive = true,
    ) {
    }

}
