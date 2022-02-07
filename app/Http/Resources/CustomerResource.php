<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray($request)
    {
        $transactions = Transaction::where('customer_id', $this->id)->get();
        $curBalance = $this->initial_balance;
        foreach ($transactions as $trans) {
            if ($trans->type == 1) {
                $curBalance -= $trans->amount;
            } else {
                $curBalance += $trans->amount;
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'initial_balance' => $this->initial_balance,
            'current_balance' => $curBalance,
            'added_by_id' => $this->added_by_id,
            'added_by_name' => $this->addedBy['name'],
            'date_added' => $this->created_at ? date('M d, Y \a\t h:i a', strtotime($this->created_at)) : null,
            'last_updated' => $this->updated_at ? date('M d, Y \a\t h:i a', strtotime($this->updated_at)) : null,
        ];
    }
}
