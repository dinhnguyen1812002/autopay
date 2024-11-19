<?php

namespace App\Models;

use App\Models\Agency\Agency;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasUlids;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //    public function assignRoles($roles)
    //    {
    //        $this->syncRoles($roles);
    //    }
    //    public function assignPermissions($permissions)
    //    {
    //        $this->syncPermissions($permissions);
    //    }
    //    public function hasAnyRole($roles): bool
    //    {
    //        return $this->hasRole($roles);
    //    }
    //
    //    public function hasAnyPermission($permissions): bool
    //    {
    //        return $this->hasPermissionTo($permissions);
    //    }
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

}
