<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BillboardFace extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'code',
        'face', 
        'location_detail', 
        'status', 
        'rented_from', 
        'available_from', 
        // 'billboard_id',
        //Migrated from billboards
        'entity_status', 
        'name', 
        'location', 
        'size', 
        'price_per_month',
        'traffic_data', 
        'latitude', 
        'longitude',
        'billboard_structure_id',
        'city_id',
        'advertiser_id',
        'zone_id'
    ];
    protected $casts = [
        'rented_from' => 'date:Y-m-d',
        'available_from' => 'date:Y-m-d'
    ];

    // public function billboard()
    // {
    //     return $this->belongsTo(Billboard::class);
    // }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('default')
            ->useFallbackUrl(asset('images/no-picture-available.jpg'))
            ->useFallbackPath(public_path('images/no-picture-available.jpg'))
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('md')
              ->width(480)
              ->height(360)
              ->sharpen(10)->nonQueued();

        $this->addMediaConversion('sm')
              ->width(240)
              ->height(180)
              ->sharpen(10)->nonQueued();
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function billboardStructure()
    {
        return $this->belongsTo(BillboardStructure::class);
    }

    public function advertiser()
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }
}
