<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory;

	protected $table = 'quote_requests';

    protected $fillable = [
		'request_id',
		'quote_id',
	];

	public function quote()
	{
		return $this->belongsTo(Quote::class, 'quote_id');		
	}

	public function request()
	{
		return $this->belongsTo(Request::class, 'request_id');
	}
}