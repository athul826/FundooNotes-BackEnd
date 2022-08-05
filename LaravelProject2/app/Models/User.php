<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function updatePassword($user, $new_password)
    {
        $user->password = bcrypt($new_password);
        $user->save();
        return $user;
    }
    public function notes()
    {
        return $this->hasMany('App\Models\Note');
    }

    public function labels()
    {
        return $this->hasmany('App\Models\Label');
    }

    public function label_notes()
    {
        return $this->hasMany('App\Models\LabelNotes');
    }
    public static function getUserByEmail($email)
    {
        $user = User::where('email', $email)->first();
        return $user;
    }
    /**
     * Accessor for first name : Mr/s. will be added while displaying
     */
    public function getFirstNameAttribute($value)
    {
        return 'Mr/s. ' .  $this->attributes['firstname'] = ucfirst($value);
    }
    /**
     * Mutator for first name : first letter of first name will changed to upper case 
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['firstname'] = ucfirst($value);
    }

    /**
     * Mutator for last name : first letter of last name will changed to upper case
     */
    public function setLastNameAttribute($value)
    {
        $this->attributes['lastname'] = ucfirst($value);
    }
}
