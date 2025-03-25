<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
		'user_id',
		'social_reason',
		'name_contact',
		'phone_contact',
		'commision_percentage',
	];
}