<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogsActivity;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, LogsActivity;

    protected $connection = 'mysql';

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'is_owner',
        'status',
        'phone',
        'position',
        'bio',
        'google_id',
        'avatar',
        'permissions',
        'two_factor_code',
        'two_factor_expires_at',
        'two_factor_enabled',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'permissions' => 'array',
        ];
    }

    /**
     * Check if the user has a specific permission.
     * Owner always has all permissions.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->is_owner) {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    public static function getAvailablePermissions()
    {
        return [
            'proposals' => [
                'label' => 'Teklif Yönetimi',
                'permissions' => [
                    'proposals.view' => 'Görüntüleme',
                    'proposals.create' => 'Oluşturma',
                    'proposals.edit' => 'Düzenleme',
                    'proposals.delete' => 'Silme',
                ]
            ],
            'customers' => [
                'label' => 'Müşteri Yönetimi',
                'permissions' => [
                    'customers.view' => 'Görüntüleme',
                    'customers.create' => 'Oluşturma',
                    'customers.edit' => 'Düzenleme',
                    'customers.delete' => 'Silme',
                ]
            ],
            'products' => [
                'label' => 'Ürün/Hizmet Yönetimi',
                'permissions' => [
                    'products.view' => 'Görüntüleme',
                    'products.create' => 'Oluşturma',
                    'products.edit' => 'Düzenleme',
                    'products.delete' => 'Silme',
                ]
            ],
            'reports' => [
                'label' => 'Raporlar',
                'permissions' => [
                    'reports.view' => 'Raporları Görüntüleme',
                ]
            ],
            'users' => [
                'label' => 'Kullanıcı Yönetimi',
                'permissions' => [
                    'users.view' => 'Görüntüleme',
                    'users.create' => 'Oluşturma',
                    'users.edit' => 'Düzenleme',
                    'users.delete' => 'Silme',
                ]
            ],
             'settings' => [
                'label' => 'Ayarlar',
                'permissions' => [
                    'settings.view' => 'Görüntüleme',
                    'settings.edit' => 'Düzenleme',
                ]
            ],
        ];
    }

    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }
}
