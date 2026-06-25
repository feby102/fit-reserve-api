<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 class VerificationRequest extends Model
{
    protected $fillable = [
        'user_id', 'role', 'price', 
          'documents', 'status',
        'rejection_reason', 'reviewed_by', 'reviewed_at'
    ];

    protected $casts = [
        'documents'   => 'array',
        'reviewed_at' => 'datetime',
    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    // مين الأدمن اللي راجعه؟
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}