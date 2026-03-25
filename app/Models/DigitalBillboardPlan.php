<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalBillboardPlan extends Model
{
    use HasFactory;

    protected $fillable = [
		'name',
		'passes_per_hour',
		'price_per_month',
		'seconds_per_day',
		'max_videos',
		'description',
	];
}