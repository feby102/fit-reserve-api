<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GymScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
       'gym_id'=>$this->gym_id,
        'start_date'=>$this->start_date,
        'end_date'=>$this->end_date,
        'day'=>$this->day,

        ];
     }
}
