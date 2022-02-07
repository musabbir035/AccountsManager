<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer' => $this->customer,
            'receipt_id' => $this->receipt_id,
            'description' => $this->description,
            'type_id' => $this->type,
            'type' => $this->type === 1 ? trans('site.income') : trans('site.expense'),
            'amount' => $this->amount,
            'due_amount' => $this->due_amount,
            'date' => date('M d, Y', strtotime($this->date)),
            'added_by_id' => $this->added_by_id,
            'added_by_name' => $this->addedBy['name'],
            'date_added' => $this->created_at ? date('M d, Y \a\t h:i a', strtotime($this->created_at)) : null,
            'last_updated' => $this->updated_at ? date('M d, Y \a\t h:i a', strtotime($this->updated_at)) : null,
        ];
    }
}
