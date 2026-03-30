<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          'user_id'=> $this->id,
        'gym_plan_id'=>$this->gym_plan_id,
        'start_date'=>$this->start_date,
        'end_date'=>$this->end_date,
        'status'=>$this->status,
        
        ];
    }
}
