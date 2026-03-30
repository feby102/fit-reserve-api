<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StadiumScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
     
        return [
             'owner_id' => $this->vendor_id,  
            'name' => $this->name,
            'description' => $this->description,
            'city' => $this->city,
            'address' => $this->address,
            'price_per_hour' => $this->price_per_hour,
             'schedules' => StadiumScheduleResource::collection($this->whenLoaded('schedules')),
             
        ];
    }
}
