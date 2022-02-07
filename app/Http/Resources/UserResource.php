<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $roleText = 'User';
        if ($this->role === 1) {
            $roleText = 'Super Admin';
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'role_id' => $this->role,
            'role' => $roleText,
            'created_at' => $this->created_at ? date('d-m-Y \a\t h:i a', strtotime($this->created_at)) : null,
            'updated_at' => $this->updated_at ? date('d-m-Y \a\t h:i a', strtotime($this->updated_at)) : null,
            'deleted_at' => $this->deleted_at ? date('d-m-Y \a\t h:i a', strtotime($this->deleted_at)) : null
        ];
    }
}
