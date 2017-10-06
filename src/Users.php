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
        "edit",
        "delete",
    ];

    function boot()
    {

        parent::boot();

        $this->registerPolices();

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


    function registerPolices(){

        /**
         * Users allowed to update:
         * Super admins are allowed to update all users
         * Users given permission to update are allowed to update all other users
         * All users allowed to update their profile
         */
        $this->gate->define("users.update", function ($user, $profile) {
            return $user->hasRole("superadmin")
                || $user->id == $profile->id
                || $user->hasAccess("users.update");
        });

        /**
         * Users allowed to delete:
         * Super admins are allowed to delete all users
         * Users given permission to delete are allowed to delete all users
         * Users are not allowed to delete themselves
         */
        $this->gate->define("users.delete", function ($user, $profile = false) {

            if ($profile) {
                return $user->hasAccess("users.delete") and $user->id != $profile->id;
            } else {
                return $user->hasAccess("users.delete");
            }

        });
    }
}
