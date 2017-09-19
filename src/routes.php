<?php

/*
 * WEB
 */
Route::group([
    "prefix" => ADMIN,
    "middleware" => ["web", "auth"]
], function ($route) {
    $route->group(["prefix" => "users"], function ($route) {
        $route->any('/', array("as" => "admin.users.show", "uses" => "Dot\Users\Controllers\UsersController@index"));
        $route->any('/create', array("as" => "admin.users.create", "uses" => "Dot\Users\Controllers\UsersController@create"));
        $route->any('/{id}/edit', array("as" => "admin.users.edit", "uses" => "Dot\Users\Controllers\UsersController@edit"));
        $route->any('/delete', array("as" => "admin.users.delete", "uses" => "Dot\Users\Controllers\UsersController@delete"));
        $route->any('/search', array("as" => "admin.users.search", "uses" => "Dot\Users\Controllers\UsersController@search"));
    });

});

/*
 * API
 */
Route::group([
    "prefix" => API,
    "middleware" => ["auth:api"]
], function ($route) {
    $route->get("/users/show", "Dot\Users\Controllers\UsersApiController@show");
    $route->post("/users/create", "Dot\Users\Controllers\UsersApiController@create");
    $route->post("/users/update", "Dot\Users\Controllers\UsersApiController@update");
    $route->post("/users/destroy", "Dot\Users\Controllers\UsersApiController@destroy");
});



