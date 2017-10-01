<?php

namespace Dot\Users\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Dot\Auth\Models\Auth;
use Hash;
use Dot\Platform\Model;
use Dot\Roles\Models\Role;
use Dot\Media\Models\Media;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{

    use Authenticatable, Authorizable, CanResetPassword;

    protected $module = 'users';

    protected $creatingRules = [
        'username' => 'required|unique:users',
        "email" => "required|email|unique:users",
        "first_name" => "required"
    ];

    protected $updatingRules = [
        "username" => "required|unique:users,username,[id],id",
        "email" => "required|email|unique:users,email,[id],id",
        "first_name" => "required"
    ];

    protected $searchable = [
        "username", "email", "first_name"
    ];

    protected $table = 'users';

    protected $guarded = array('id', "permission");

    protected $hidden = array();


    function setCreateValidation($v)
    {
        $v->sometimes(["password", "repassword"], "required|same:repassword", function ($input) {
            return $input->provider == NULL;
        });

        return $v;
    }

    function setUpdateValidation($v)
    {
        $v->sometimes(["password", "repassword"], "required|same:repassword", function ($input) {
            return $input->provider == NULL and $input->password != "";
        });

        return $v;
    }

    function setPasswordAttribute($password)
    {
        if (trim($password) != "") {
            $this->attributes["password"] = Hash::make($password);
        } else {
            unset($this->attributes["password"]);
        }
    }

    function setRepasswordAttribute($password)
    {
        unset($this->attributes["repassword"]);
    }

    public function permissions()
    {
        return $this->hasMany('UserPermission', "user_id", "id");
    }

    public function can($ability, $arguments = [])
    {
        $this->access($ability);
    }

    public function groups()
    {
        return $this->belongsToMany('Group', 'users_groups', 'group_id', 'user_id');
    }

    public function getNameAttribute()
    {
        $name = $this->first_name . ' ' . $this->last_name;

        if (trim($name) == "") {
            return $this->username;
        }

        return $name;
    }


    public function getFirstNameAttribute($value)
    {
        return ($value) ? $value : '';
    }

    public function getLastNameAttribute($value)
    {
        return ($value) ? $value : '';
    }

    public function photo()
    {
        return $this->hasOne(Media::class, 'id', 'photo_id');
    }

    public function getPhotoUrlAttribute()
    {

        if (Auth::guard(GUARD)->user()->photo) {
            return thumbnail(Auth::guard(GUARD)->user()->photo->path, "thumbnail", "admin::images/author.png");
        } else {
            return assets("admin::images/author.png");
        }
    }

    public function role()
    {
        return $this->hasOne(Role::class, "id", 'role_id');
    }

    public function scopeRoot($query){
        $query->where("root", 1);
    }


    public function hasRole($role = "")
    {

        $string = strtolower($role);

        // get authenticated user
        $user = Auth::guard(GUARD)->user();

        $role_name = "";

        if ($user->role) {
            $role_name = strtolower($user->role->name);
        }

        if ($string == $role_name) {
            return (bool)true;
        }

        return (bool)false;
    }

    public function hasAccess($params = array())
    {

        if ($this->hasRole("superadmin")) {
            return true;
        }

        $params = is_array($params) ? $params : func_get_args();

        // get authenticated user
        $user = Auth::guard(GUARD)->user();

        $permissions = [];

        if ($user->role) {
            $permissions = (array)$user->role->permissions->pluck("permission")->toArray();
        }

        if (count($permissions) == 0) {
            return false;
        }

        $permissions_string = join("", $permissions);

        if (count($params)) {
            foreach ($params as $param) {
                if (!in_array($param, $permissions)) {
                    if (!strstr($permissions_string, $param . ".")) {
                        return false;
                    }
                }
            }
            return true;
        }

        return false;
    }

    function newApiToken()
    {
        return str_random(60);
    }


    /*

public static function method($name = false, $callback)
{
    Config::set("methods." . $name, $callback);
}

public function __call($name, $arguments)
{

    if (Config::has("methods." . $name)) {
        $callback = Config::get("methods." . $name);
        return $callback($this);
    }

    return $this->$name();
}


public function __call($method, $parameters = array())
{
    if (starts_with($method, 'isNot') and $method != 'isNot') {
        return $this->isNot(snake_case(substr($method, 2)));
    } elseif (starts_with($method, 'is') and $method != 'is') {
        return $this->is(snake_case(substr($method, 2)));
    }

    return $method($parameters);
}*/
}
