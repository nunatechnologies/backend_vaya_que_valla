<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillboardFaceStatusLog extends Model
{
    use HasFactory;

    protected $table = 'billboard_face_status_logs';

    protected $fillable = [
        'billboard_face_id',
        'from_status',
        'to_status',
        'user_id',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function billboardFace()
    {
        return $this->belongsTo(BillboardFace::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
