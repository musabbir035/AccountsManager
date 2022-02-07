<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\History
 *
 * @property int $id
 * @property string $historyable_type
 * @property int $historyable_id
 * @property string $old_value
 * @property string $new_value
 * @property int $action_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $action_by
 * @property-read Model|\Eloquent $historyable
 * @mixin \Eloquent
 */
class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_value',
        'new_value',
        'action_by_id'
    ];

    public function action_by()
    {
        return $this->belongsTo(User::class, 'action_by_id', 'id');
    }

    public function historyable()
    {
        return $this->morphTo();
    }
}
