<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Dot\Users\Models\User;

class MakeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // delete original users table

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('password')->nullable()->index();
            $table->string('email')->nullable()->unique();
            $table->string('first_name')->nullable()->index();
            $table->string('last_name')->nullable()->index();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable()->index();
            $table->string('provider')->nullable()->index();
            $table->string('provider_id')->nullable()->index();
            $table->string('api_token', 60)->nullable()->unique();
            $table->string('code')->nullable()->index();
            $table->string('remember_token')->nullable()->index();
            $table->integer('role_id')->default(0)->index();
            $table->integer('last_login')->nullable()->index();
            $table->integer('status')->default(0)->index();
            $table->integer('backend')->default(0)->index();
            $table->integer('root')->default(0)->index();
            $table->integer('photo_id')->default(0)->index();
            $table->string('lang', 5)->default("en")->index();
            $table->string('color', 20)->default("blue")->index();
            $table->text('about')->nullable();
            $table->string('facebook')->nullable()->index();
            $table->string('twitter')->nullable()->index();
            $table->string('linked_in')->nullable()->index();
            $table->string('google_plus')->nullable()->index();
        });

        // create default administrator user

        if (! User::where("root", 1)->count()) {

            $user = new User();
            $user->username = "admin";
            $user->password = "admin";
            $user->email = "dot@platform.com";
            $user->first_name = "admin";
            $user->last_name = "";
            $user->lang = App::getLocale();
            $user->api_token = $user->newApiToken();
            $user->status = 1;
            $user->role_id = 1;
            $user->backend = 1;
            $user->root = 1;
            $user->save();

        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
