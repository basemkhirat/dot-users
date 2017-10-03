<?php

namespace Dot\Users;

use Action;
use Dot\Users\Models\User;
use Gate;
use Navigation;

class Users extends \Dot\Platform\Plugin
{

    protected $permissions = [
        "manage"
    ];

    function boot()
    {

        parent::boot();

        Navigation::menu("sidebar", function ($menu) {

            if (Gate::allows('users')) {
                $menu->item('users', trans("admin::common.users"), "#")
                    ->order(16)
                    ->icon("fa-users");

                $menu->item('users.all', trans("admin::common.users"), route("admin.users.show"));
            }
        });

        Action::listen("dashboard.featured", function () {

            $users = User::orderBy("created_at", "DESC")->limit(5)->get();

            return view("users::widgets.users", ["users" => $users]);

        });
    }
}
