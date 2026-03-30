<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GymplansResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      return [
        'name'=>$this->name,
          'gym_id'=> $this->gym_id,
        'duration'=>$this->duration,
        'name'=>$this->name,
        'price'=>$this->price,
        
        
        ];
    }
}
