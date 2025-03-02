<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $table = 'users';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = ['password','remember_token','verification_code','email_verified_at',];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'code',
        'username',
        'userType',
        'role',
        'phone_number',
        'registration_number',
        'status_id',
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function boot()
    {
        parent::boot();


        static::creating(function ($model) {
            if(!$model->id){
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }

            if (in_array('code',$model->fillable) && empty($model->code))
                $model->code= str_pad(DB::table($model->table)->count()+1, 4, '0', STR_PAD_LEFT);

//            if (in_array('status_id',$model->fillable))
//                $model->status_id=Status::where('code','ACTIVE')->first()->id;
        });

        Pivot::creating(function($pivot) {
            $pivot->id = (string) Str::uuid();

        });

        static::retrieved(function ($model) {


            if(Schema::hasColumn($model->table, 'first_name')){

                if (is_string($model->first_name)){
                    $model->first_name =trim(strtoupper($model->first_name));
                }else{
                    $model->first_name ="";
                }
            }

            if(Schema::hasColumn($model->table, 'middle_name')){
                if (is_string($model->middle_name)){
                    $model->middle_name =trim(strtoupper($model->middle_name));
                }else{
                    $model->middle_name ="";
                }
            }

            if(Schema::hasColumn($model->table, 'last_name')){
                if (is_string($model->last_name)){
                    $model->last_name =trim(strtoupper($model->last_name));
                }else{
                    $model->last_name ="";
                }
            }

            if(Schema::hasColumn($model->table, 'name')){
                if (is_string($model->name)){
                    $model->name =trim(strtoupper($model->name));
                }else{
                    $model->name ="";
                }
            }

            if(Schema::hasColumn($model->table, 'email')){
                if (is_string($model->email)){
                    $model->email =trim(strtoupper($model->email));
                }else{
                    $model->email="";
                }
            }
        });
    }
//    public function role(): BelongsTo
//    {
//        return $this->belongsTo(Role::class, 'role_id');
//    }

    public function findForPassport($username)
    {
        return $this->where('email', $username)->orWhere('username', $username)->orWhere('phone_number', $username)->first();
    }

    public function wallet(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }



}
