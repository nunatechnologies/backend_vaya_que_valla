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
		'nit',
		'name_contact',
		'phone_contact',
		'commision_percentage',
		'category_id'
	];
	
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function category()
	{
		return $this->belongsTo(Category::class, 'category_id');
	}
}