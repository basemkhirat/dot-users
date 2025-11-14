<?php

/*
 * WEB
 */

Route::group([
    "prefix" => ADMIN,
    "middleware" => ["web", "auth:backend"],
    "namespace" => "Dot\\Users\\Controllers"
], function ($route) {

    $route->group(["prefix" => "users"], function ($route) {
        $route->any('/', ["as" => "admin.users.show", "uses" => "UsersController@index"])
            ->middleware('can:users.show');
        $route->any('/create', ["as" => "admin.users.create", "uses" => "UsersController@create"])
            ->middleware('can:users.create');
        $route->any('/{id}/edit', ["as" => "admin.users.edit", "uses" => "UsersController@edit"]);
        $route->any('/delete', ["as" => "admin.users.delete", "uses" => "UsersController@delete"])
            ->middleware('can:users.delete');
        $route->any('/search', ["as" => "admin.users.search", "uses" => "UsersController@search"]);
    });

});
