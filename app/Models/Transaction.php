<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property int|null $customer_id
 * @property int|null $receipt_id
 * @property string $description
 * @property int $type
 * @property string $amount
 * @property string|null $due_amount
 * @property string $date
 * @property int $added_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $addedBy
 * @property-read \App\Models\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\History[] $histories
 * @property-read \App\Models\Receipt|null $receipt
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction find()
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'type', //1 for income or 2 for expense
        'amount',
        'date',
        'receipt_id',
        'customer_id'
    ];

    public static $INCOME = 1;
    public static $EXPENSE = 2;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
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
