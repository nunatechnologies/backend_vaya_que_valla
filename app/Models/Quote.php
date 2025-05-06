<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'billboard_face_id',
        'status',
        'start_date',
        'end_date',
        'total_amount',
        'months'
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function billboardFace()
    {
        return $this->belongsTo(BillboardFace::class, 'billboard_face_id');
    }
}
