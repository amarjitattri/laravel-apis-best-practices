<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'userId' => $this->id,
            'name' => strtoupper($this->name),
            'email' => $this->email,
            'accessToken' => $this->createToken('admin-token', ['create', 'update', 'delete'])->plainTextToken,
            'updateToken' => $this->createToken('update-token', ['create', 'update'])->plainTextToken,
            'basic' => $this->createToken('basic-token', ['read'])->plainTextToken,
        ];
    }
}
