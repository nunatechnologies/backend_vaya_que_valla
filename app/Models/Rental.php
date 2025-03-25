<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
		'user_id',
		'quote_id',
		'starts_at',
		'ends_at',
		'status',
		'has_lona',
		'total_amount',
		'unsubscribe_status',
	];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }
}