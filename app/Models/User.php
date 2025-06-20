<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\UUID;
use App\Enums\ApiErrorCode;
use Illuminate\Support\Str;
use App\Traits\HasTwoFaTrait;
use App\Traits\MorphMapTrait;
use App\Traits\ActivityLogTrait;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use App\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Storage;
use App\Contracts\Auth\MustSatisfyTwoFa;
use Illuminate\Notifications\Notifiable;
use App\Traits\EmailVerificationCodeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements
    MustVerifyEmail,
    MustSatisfyTwoFa
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
    use EmailVerificationCodeTrait;
    use UUID;
    use MorphMapTrait;
    use HasTwoFaTrait;
    // use ActivityLogTrait;
    use SoftDeletes {
        restore as public restoreFromTrait;
    }

   /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'phone',
        'avatar',
        'address',
        'zipcode',
        'ssn',
        'dob',
        'nationality',
        'experience',
        'employed',
        'status',
        'id_type',
        'id_number',
        'kyc',
        'front_id',
        'back_id',
        'password',
        'blocked_at',
        'country_id',
        'state_id',
        'city',
        'currency_id',
        'email_verified_at',
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
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'password' => 'hashed',
        'blocked_at' => 'datetime',
    ];

    /**
     * Append the accessor attributes to the model's array form.
     *
     * @var array
     */
    protected $appends = ['front_id', 'back_id'];

    protected static function booted()
    {
        static::created(function ($user) {
            
            UserSettings::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
            ]);

        });
    }

    /**
     * Get the user's avatar.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset($value),
        );
    }

    /**
     * Get the full URL for the front_id.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function frontId(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['front_id'] ? asset($attributes['front_id']) : null,
        );
    }

    /**
     * Get the full URL for the back_id.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function backId(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['back_id'] ? asset($attributes['back_id']) : null,
        );
    }

    /**
     * Get the user's full name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }

    /**
     * Interact with the user's email.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? strtolower($value) : $value,
            set: fn ($value) => $value ? strtolower($value) : $value,
        );
    }

    /**
     * Interact with the user's username.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function username(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? strtolower($value) : $value,
            set: fn ($value) => $value ? strtolower($value) : $value,
        );
    }

    /**
     * Get the name of the log.
     *
     * @return string|null
     */
    public function getActivityLogTitle(): string|null
    {
        return $this->full_name;
    }

    /**
     * Toggle blocked_at column.
     *
     * @return void
     */
    public function toggleBlock(): void
    {
        $this->blocked_at = $this->blocked_at ? null : now();
        $this->saveOrFail();
    }

    /**
     * Delete the model from the database.
     *
     * @param string|null $reason
     * @return bool|null
     *
     * @throws \LogicException
     */
    public function deletes(string|null $reason = null)
    {
        $this->updateQuietly([
            'deleted_reason' => $reason,
        ]);

        $this->tokens()->delete();

        parent::delete();
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool
     */
    public function restore(): bool
    {
        $this->deleted_reason = null;

        return $this->restoreFromTrait();
    }

    /**
     * Scope a query to filter results based on 'blocked_at' status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $active
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeBlocked($query, bool $active = true): \Illuminate\Database\Eloquent\Builder
    {
        return $query->when(
            $active,
            fn ($query) => $query->whereNotNull('blocked_at'),
            fn ($query) => $query->whereNull('blocked_at')
        );
    }

    /**
     * Scope a query to filter results based on 'email_verified_at' status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $active
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeEmailVerified($query, bool $active = true): \Illuminate\Database\Eloquent\Builder
    {
        return $query->when(
            $active,
            fn ($query) => $query->whereNotNull('email_verified_at'),
            fn ($query) => $query->whereNull('email_verified_at')
        );
    }

    /**
     * Scope a query to filter based on full name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeName($query, string $name): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
    }

    /**
     * Scope filter between registration dates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array<int, string> ...$dates
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRegistrationDate($query, ...$dates): \Illuminate\Database\Eloquent\Builder
    {
        $from = head($dates);
        $to = count($dates) > 1 ? $dates[1] : null;

        return $query->where(
            fn ($query) => $query->whereDate('created_at', '>=', $from)
                ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to)) // @phpstan-ignore-line
        );
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\Auth\ResetPasswordNotification($token));
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function transactionsFetch(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function notifiable()
    {
        return $this->hasMany(Notification::class);
    }
    
    // public function transactions()
    // {
    //     return $this->morphMany(Transaction::class, 'transactable');
    // }

    public function storeTransaction(
        float $amount,
        string $transactableId,
        string $transactableType,
        string $type,
        ?string $status = 'pending',
        ?string $comment = null,
        ?string $swap_from = null,
        ?string $swap_to = null,
        string $created_at,
    ): Transaction {
        return $this->transactions()->create([
            'user_id'           => $this->id,
            'amount'            => $amount,
            'transactable_id'   => $transactableId,
            'transactable_type' => $transactableType,
            'type'              => $type,
            'status'            => $status,
            'comment'           => $comment,
            'swap_from'           => $swap_from,
            'swap_to'           => $swap_to,
            'created_at'           => $created_at,
        ]);
    }

    public function storeNotification(
        string $message,
    ): Notification {
        return $this->notifiable()->create([
            'user_id'           => $this->id,
            'message'            => $message,
        ]);
    }

    public function savings()
    {
        return $this->hasMany(Savings::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function trade()
    {
        return $this->hasMany(Trade::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function placeTrade(array $data): Trade
    {
        $asset = Asset::findOrFail($data['asset_id']);

        if(!$asset) {
            throw new \Exception("Error fetching the asset, data does not exist.");
        }

        return $this->trade()->create([
            'asset_id' => $data['asset_id'],
            'asset_type' => $data['asset_type'], // 'crypto' or 'stock'
            'type' => $data['type'],            // 'buy' or 'sell'
            'price' => $asset->price,
            'quantity' => $data['quantity'],
            'amount' => $data['amount'],
            'status' => $data['status'] ?? 'open',
            'entry' => $data['entry'] ?? null,
            'exit' => $data['exit'] ?? null,
            'leverage' => $data['leverage'] ?? null,
            'interval' => $data['interval'] ?? null,
            'tp' => $data['tp'] ?? null,
            'sl' => $data['sl'] ?? null,
            'extra' => $data['extra'] ?? 0,
            'created_at' => $data['created_at'] ?? now(),
        ]);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function depositAccount(): HasOne
    {
        return $this->hasOne(Payment::class)->where('type', 'admin');
    }

    public function withdrawalAccount(): HasOne
    {
        return $this->hasOne(Payment::class)->where('type', 'user');
    }

    public function storePayment(string $type, array $data): Payment
    {
        // Ensure the user has only one of each type
        if ($this->payments()->where('type', $type)->exists()) {
            throw new \Exception("User already has a payment of type {$type}");
        }

        return $this->payments()->create(array_merge([
            'id' => Str::uuid(),
            'type' => $type,
            'wallet_name' => null,
            'wallet_address' => null,
            'wallet_note' => null,
            'bank_name' => null,
            'account_name' => null,
            'bank_number' => null,
            'bank_account_number' => null,
            'bank_routing_number' => null,
            'bank_reference' => null,
            'bank_address' => null,
        ], $data));
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function depositMethods()
    {
        return $this->paymentMethods()->where('is_withdrawal', false);
    }

    public function withdrawalMethods()
    {
        return $this->paymentMethods()->where('is_withdrawal', true);
    }

    public function cryptoMethods()
    {
        return $this->paymentMethods()->where('type', 'crypto');
    }

    public function bankMethods()
    {
        return $this->paymentMethods()->where('type', 'bank');
    }

    public function settings()
    {
        return $this->hasOne(UserSettings::class);
    }

    public function beneficiary()
    {
        return $this->hasOne(UserSettings::class);
    }

    public function autoPlanInvestment()
    {
        return $this->hasMany(AutoPlanInvestment::class);
    }

}
