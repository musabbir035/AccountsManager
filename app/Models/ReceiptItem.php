<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ReceiptItem
 *
 * @property int $id
 * @property int $product_id
 * @property int $receipt_id
 * @property int $quantity
 * @property string $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Receipt $receipt
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptItem whereReceiptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptItem whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'total'
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
