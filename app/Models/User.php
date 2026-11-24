<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $attributes = ['role' => 'admin'];

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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(StoreOrder::class);
    }

    /**
     * Restore a guest cart after a successful sign-in or registration.
     *
     * @param  array<int|string, int>  $pendingCart
     */
    public function mergePendingCart(array $pendingCart): bool
    {
        $cartWasAdjusted = false;

        DB::transaction(function () use ($pendingCart, &$cartWasAdjusted): void {
            $user = self::query()->whereKey($this->id)->lockForUpdate()->firstOrFail();

            foreach ($pendingCart as $bouquetId => $quantity) {
                if (! ctype_digit((string) $bouquetId) || ! is_int($quantity) || $quantity < 1) {
                    $cartWasAdjusted = true;

                    continue;
                }

                $bouquet = Bouquet::query()->whereKey($bouquetId)->where('is_active', true)->exists();

                if (! $bouquet) {
                    $cartWasAdjusted = true;

                    continue;
                }

                $item = $user->cartItems()->where('bouquet_id', $bouquetId)->lockForUpdate()->first();
                $totalQuantity = ($item?->quantity ?? 0) + $quantity;

                if ($totalQuantity > 100) {
                    $cartWasAdjusted = true;
                    $totalQuantity = 100;
                }

                $user->cartItems()->updateOrCreate(
                    ['bouquet_id' => $bouquetId],
                    ['quantity' => $totalQuantity],
                );
            }
        });

        return $cartWasAdjusted;
    }
}
