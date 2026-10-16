<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'status',
        'branch_office_id',
        'person_id',
        'user_type_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function user_type() {
        return $this->belongsTo(UserType::class);
    }

    public function branch_office() {
        return $this->belongsTo(BranchOffice::class);
    }

    public function person() {
        return $this->belongsTo(Person::class);
    }

    public function checkouts() {
        return $this->hasMany(Checkout::class);
    }


    public function get_menu_options(){

        return MenuOption::join('menu_categories', 'menu_categories.id', '=', 'menu_options.menu_category_id')
            ->join('permissions', 'permissions.menu_option_id', '=', 'menu_options.id')
            ->where('permissions.user_type_id', $this->user_type->id)
            ->orderBy('menu_categories.order', 'asc')
            ->orderBy('menu_options.order', 'asc')
            ->select(
                'menu_options.*',
                'menu_categories.icon as menu_category_icon',
                'menu_categories.name as menu_category_name',
            );
    }


    public function scopeSearch($query, $username, $branch_office_id, $user_type_id, $person_name){

        $query = $query->join('branch_offices', 'branch_offices.id', 'users.branch_office_id')
            ->join('user_types', 'user_types.id', 'users.user_type_id')
            ->join('people', 'people.id', 'users.person_id')
            ->where(function ($subquery) use ($person_name) {
                if (!is_null($person_name)) {
                    $subquery->where('people.name', 'ILIKE', '%' . $person_name . '%')
                        ->orWhere('people.surname_father', 'ILIKE', '%' . $person_name . '%')
                        ->orWhere('people.surname_mother', 'ILIKE', '%' . $person_name . '%')
                        ->orWhere('people.business_name', 'ILIKE', '%' . $person_name . '%')
                        ->orWhere('people.dni', 'ILIKE', '%' . $person_name . '%')
                        ->orWhere('people.ruc', 'ILIKE', '%' . $person_name . '%')
                        ->orWhere(DB::raw("CONCAT(people.name, ' ', people.surname_father, ' ', people.surname_mother)"), 'ILIKE', '%' . $person_name . '%')
                        ->orWhere(DB::raw("CONCAT(people.surname_father, ' ', people.surname_mother, ' ', people.name)"), 'ILIKE', '%' . $person_name . '%');
                }
            })
            ->where(function ($subquery) use ($username) {
                if (!is_null($username)) {
                    $subquery->where('users.username', 'ILIKE', '%' . $username . '%');
                }
            })->where(function ($subquery) use ($branch_office_id) {
                if (!is_null($branch_office_id)) {
                    $subquery->where('users.branch_office_id', '=', $branch_office_id);
                }
            })->where(function ($subquery) use ($user_type_id) {
                if (!is_null($user_type_id)) {
                    $subquery->where('users.user_type_id', '=', $user_type_id);
                }
            });

        return $query;
    }
}
