<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_CUSTOMER = 'customer';

    public const ROLE_EMPLOYEE = 'employee';

    public const ROLE_ADMIN = 'master_admin';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'employee_code',
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
        ];
    }

    public function isMasterAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isEmployee(): bool
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function roleDashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_EMPLOYEE => 'employee.dashboard',
            default => 'customer.dashboard',
        };
    }

    /**
     * @return HasMany<Account>
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'customer_id');
    }

    /**
     * @return BelongsToMany<Branch>
     */
    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class)
            ->withPivot(['position', 'assigned_at'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Transaction>
     */
    public function performedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'performed_by');
    }

    /**
     * @return HasMany<EmployeeAction>
     */
    public function employeeActions(): HasMany
    {
        return $this->hasMany(EmployeeAction::class, 'employee_id');
    }

    /**
     * @return HasMany<EmployeeAction>
     */
    public function subjectActions(): HasMany
    {
        return $this->hasMany(EmployeeAction::class, 'subject_user_id');
    }
}
