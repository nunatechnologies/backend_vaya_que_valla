<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
		'user_id',
		'company',
        'status',
        'description',
        'budget_description',
        'tentative_start_date'
	];

    protected $casts = [
        'tentative_start_date' => 'date:Y-m-d'
    ];

	public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quotes(): BelongsToMany
    {
        return $this->belongsToMany(Quote::class, 'quote_requests')
                    ->withTimestamps();
    }
}