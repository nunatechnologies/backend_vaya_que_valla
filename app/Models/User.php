<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\AccountActived;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\VerifyApiEmail;
use App\Notifications\ResetPasswordCustom;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail, HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use  HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens, InteractsWithMedia;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $with = ['organization', 'person'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'cod_phone',
        'phone',
        'email',
        'password',
        'user_type',
        'entity_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFullNameAttribute()
    {
        return ucwords($this->name.' '.$this->last_name);
    }

    public function person()
    {
        return $this->hasOne(Person::class, 'user_id');
    }

    public function organization()
    {
        return $this->hasOne(Organization::class, 'user_id');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyApiEmail());
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordCustom($token));
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('default')
            ->useFallbackUrl(asset('images/default-avatar.png'))
            ->useFallbackPath(public_path('images/default-avatar.png'))
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

    public function markAccountAsVerified()
    {
        $this->entity_status = 'active';
        $this->save();
        $this->notify(new AccountActived());
    }
}
