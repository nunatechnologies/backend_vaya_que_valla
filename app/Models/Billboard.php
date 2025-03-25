<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_status', 'name', 'location', 'size', 'price_per_month',
        'status', 'traffic_data', 'image', 'latitude', 'longitude',
        'billboard_type_id','billboard_structure_id', 'city_id', 'advertiser_id',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function billboardType()
    {
        return $this->belongsTo(BillboardType::class);
    }

    public function advertiser()
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }
}
