<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillboardFace extends Model
{
    use HasFactory;

    protected $fillable = ['face', 'location_detail', 'billboard_id'];

    public function billboard()
    {
        return $this->belongsTo(Billboard::class);
    }
}
