<?php

namespace Dot\Users;

use Action;
use Dot\Users\Models\User;
use Illuminate\Support\Facades\Auth;
use Navigation;

class Users extends \Dot\Platform\Plugin
{

    protected $permissions = [
        "show",
        "create",
        "update",
        "delete",
    ];

    function boot()
    {

        parent::boot();

        $this->gate->define("users.update", function($user, $profile){
            return $user->hasRole("superadmin") || $user->id == $profile->id;
        });

        $this->gate->define("users.delete", function($user, $profile = false){

            if($profile) {
                return $user->hasAccess("users.delete") and $user->id != $profile->id;
            }else{
                return $user->hasAccess("users.delete");
            }

        });

        Navigation::menu("sidebar", function ($menu) {

            if (Auth::user()->can('users')) {

                $menu->item('users', trans("admin::common.users"), route("admin.users.show"))
                    ->order(16)
                    ->icon("fa-users");
            }

        });

        Action::listen("dashboard.featured", function () {

            if (Auth::user()->can('users')) {

                $users = User::orderBy("created_at", "DESC")->limit(5)->get();

                return view("users::widgets.users", ["users" => $users]);

            }

        });
    }
}
