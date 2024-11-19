<?php

namespace App\Models\Agency;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'website_url',
        'logo_url',
        'support_email',
        'custom_domain',
        'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);

    }
}
