<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Receipt
 *
 * @property int $id
 * @property int|null $customer_id
 * @property string $date
 * @property string|null $name
 * @property string|null $mobile
 * @property string|null $address
 * @property string|null $total
 * @property string|null $due_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $addedBy
 * @property-read \App\Models\Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\History[] $histories
 * @property-read int|null $histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ReceiptItem[] $receiptItems
 * @property-read int|null $receipt_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt query()
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereDueAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Receipt whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'name',
        'mobile',
        'address',
        'total',
        'due_amount'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function receiptItems()
    {
        return $this->hasMany(ReceiptItem::class);
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
