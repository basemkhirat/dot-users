<?php

namespace Dot\Users;

use Action;
use Dot\Users\Models\User;
use Gate;
use Navigation;

class Plugin extends \Dot\Platform\Plugin
{

    public $permissions = [
        "manage"
    ];

    /**
     * @return array
     */
    function info()
    {

        return [
            "name" => "users",
            "version" => "1.0",
        ];

    }

    function boot()
    {

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

        include __DIR__ . "/routes.php";

    }
}
