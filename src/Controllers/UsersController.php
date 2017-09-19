<?php

namespace Dot\Users\Controllers;

use Request;
use Gate;
use Dot;
use View;
use Auth;
use Action;
use Redirect;
use Config;
use Session;
use Dot\Users\Models\User;
use Dot\Roles\Models\Role;
use Dot\Controller;

/**
 * Class UsersController
 */
class UsersController extends Controller
{

    /**
     * @var array
     */
    public $data = array();

    /**
     * @return mixed
     */
    public function index()
    {

        if (!Gate::allows("users.manage")) {
            Dot::forbidden();
        }

        if (Request::isMethod("post")) {
            if (Request::has("action")) {
                switch (Request::get("action")) {
                    case "delete":
                        return $this->delete();
                }
            }
        }

        $this->data["sort"] = (Request::has("sort")) ? Request::get("sort") : "created_at";
        $this->data["order"] = (Request::has("order")) ? Request::get("order") : "DESC";
        $this->data['per_page'] = (Request::has("per_page")) ? Request::get("per_page") : 20;

        $query = User::with("role", "photo")->orderBy($this->data["sort"], $this->data["order"]);

        if (Request::has("q")) {
            $q = urldecode(Request::get("q"));
            $query->search($q);
        }

        if (Request::has("per_page")) {
            $this->data["per_page"] = $per_page = Request::get("per_page");
        } else {
            $this->data["per_page"] = $per_page = 20;
        }

        if (Request::has("backend") and Request::get("backend") == 1) {
            $query->where("role_id", "!=", 0);
        }

        if (Request::has("status")) {
            $query->where("status", Request::get("status"));
        }

        if (Request::has("role_id")) {
            $query->where("role_id", Request::get("role_id"));
        }

        $this->data["users"] = $users = $query->paginate($per_page);

        /*

        $customFields = [];
        foreach($users as $user){
            $fields = Action::fire("user.table.fields", $user);
            if(count($fields)){
                foreach($fields as $field){
                    $customFields[] = $field;
                }

            }
        }

        $this->data["customFields"] = $customFields;
        */

        $this->data["roles"] = Role::all();

        return View::make("users::show", $this->data);
    }

    /**
     * @return string
     */
    public function create()
    {

        if (!Gate::allows("users.manage")) {
            Dot::forbidden();
        }

        if (Request::isMethod("post")) {

            $user = new User();

            $user->username = Request::get("username");
            $user->password = Request::get("password");
            $user->repassword = Request::get("repassword");
            $user->email = Request::get("email");
            $user->first_name = Request::get("first_name");
            $user->last_name = Request::get("last_name");
            $user->about = Request::get("about");
            $user->role_id = Request::get("role_id", 0);
            $user->photo_id = Request::get("photo_id", 0);
            $user->lang = Request::get("lang");
            $user->color = Request::get("color", "blue");
            $user->status = Request::get("status", 0);
            $user->facebook = Request::get("facebook");
            $user->twitter = Request::get("twitter");
            $user->linked_in = Request::get("linked_in");
            $user->google_plus = Request::get("google_plus");
            $user->backend = 1;

            // Fire user creating action
            Action::fire("user.saving", $user);

            if (!$user->validate()) {
                return Redirect::back()->withErrors($user->errors())->withInput(Request::all());
            }

            $user->save();

            // Fire user created action
            Action::fire("user.saved", $user);

            return Redirect::route("admin.users.edit", array("id" => $user->id))
                ->with("message", trans("users::users.events.created"));

        }

        $this->data["user"] = false;
        $this->data["roles"] = Role::all();

        return View::make("users::edit", $this->data);
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function edit($user_id)
    {

        if (Auth::user()->id != $user_id) {
            if (!Gate::allows("users.manage")) {
                Dot::forbidden();
            }
        }

        $user = User::with("photo")->where("id", $user_id)->first();

        if (count($user) == 0) {
            abort(404);
        }

        if (Request::isMethod("post")) {

            $user->username = Request::get("username");
            $user->password = Request::get("password");
            $user->repassword = Request::get("repassword");
            $user->email = Request::get("email");
            $user->first_name = Request::get("first_name");
            $user->last_name = Request::get("last_name");
            $user->about = Request::get("about");
            $user->role_id = Request::get("role_id", 0);
            $user->photo_id = Request::get("photo_id", 0);
            $user->lang = Request::get("lang");
            $user->color = Request::get("color", "blue");
            $user->status = Request::get("status", 0);
            $user->facebook = Request::get("facebook");
            $user->twitter = Request::get("twitter");
            $user->linked_in = Request::get("linked_in");
            $user->google_plus = Request::get("google_plus");

            // Fire user creating action
            Action::fire("user.saving", $user);

            if (!$user->validate()) {
                return Redirect::back()->withErrors($user->errors())->withInput(Request::all());
            }

            $user->save();

            if ($user->id == Auth::user()->id) {
                $user_lang = $user->lang;
                if (in_array($user_lang, array_keys(Config::get("admin.locales")))) {
                    Session::put('locale', $user_lang);
                }
            }

            // Fire user updated action
            Action::fire("user.saved", $user);

            return Redirect::route("admin.users.edit", array("id" => $user->id))
                ->with("message", trans("users::users.events.updated"));

        }

        $this->data["user"] = $user;
        $this->data["roles"] = Role::all();

        return View::make("users::edit", $this->data);
    }

    /**
     * @return string
     */
    function search()
    {
        $data = User::search(urldecode(Request::get("term")))
            ->take(6)
            ->get();
        return json_encode($data);
    }

    /**
     * @return string
     */
    public function delete()
    {

        if (!Gate::allows("users.manage")) {
            Dot::forbidden();
        }

        $ids = Request::get("id");
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        foreach ($ids as $ID) {
            $user = User::findOrFail($ID);

            // Fire user deleting action
            Action::fire("user.deleting", $user);

            $user->delete();

            // Fire user deleted action
            Action::fire("user.deleted", $user);
        }
        return Redirect::back()->with("message", trans("users::users.events.deleted"));
    }

    function gradient($w = 100, $h = 100, $c = array('#FFFFFF', '#FF0000', '#00FF00', '#0000FF'), $hex = true)
    {

        /*
        Generates a gradient image

        Author: Christopher Kramer

        Parameters:
        w: width in px
        h: height in px
        c: color-array with 4 elements:
            $c[0]:   top left color
            $c[1]:   top right color
            $c[2]:   bottom left color
            $c[3]:   bottom right color

        if $hex is true (default), colors are hex-strings like '#FFFFFF' (NOT '#FFF')
        if $hex is false, a color is an array of 3 elements which are the rgb-values, e.g.:
        $c[0]=array(0,255,255);

        */

        $im = imagecreatetruecolor($w, $h);

        if ($hex) {  // convert hex-values to rgb
            for ($i = 0; $i <= 3; $i++) {
                $c[$i] = $this->hex2rgb($c[$i]);
            }
        }

        $rgb = $c[0]; // start with top left color
        for ($x = 0; $x <= $w; $x++) { // loop columns
            for ($y = 0; $y <= $h; $y++) { // loop rows
                // set pixel color
                $col = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
                imagesetpixel($im, $x - 1, $y - 1, $col);
                // calculate new color
                for ($i = 0; $i <= 2; $i++) {
                    $rgb[$i] =
                        $c[0][$i] * (($w - $x) * ($h - $y) / ($w * $h)) +
                        $c[1][$i] * ($x * ($h - $y) / ($w * $h)) +
                        $c[2][$i] * (($w - $x) * $y / ($w * $h)) +
                        $c[3][$i] * ($x * $y / ($w * $h));
                }
            }
        }
        return $im;
    }

    function hex2rgb($hex)
    {
        $rgb[0] = hexdec(substr($hex, 1, 2));
        $rgb[1] = hexdec(substr($hex, 3, 2));
        $rgb[2] = hexdec(substr($hex, 5, 2));
        return ($rgb);
    }


}
