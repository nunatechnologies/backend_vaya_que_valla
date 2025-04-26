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

    protected $fillable = ['code','face', 'location_detail', 'status', 'billboard_id'];

    public function billboard()
    {
        return $this->belongsTo(Billboard::class);
    }

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
}
