<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Customer
 *
 * @property int $id
 * @property string $name
 * @property string $mobile
 * @property string|null $address
 * @property string|null $email
 * @property string $initial_balance
 * @property int $added_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $addedBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\History[] $histories
 * @property-read int|null $histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Receipt[] $receipts
 * @property-read int|null $receipts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Database\Factories\CustomerFactory factory(...$parameters)
 * @mixin \Eloquent
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mobile',
        'address',
        'email',
        'initial_balance'
    ];

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by_id', 'id');
    }

    public function histories()
    {
        return $this->morphMany(History::class, 'historyable');
    }
}
