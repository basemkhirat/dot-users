@extends("admin::layouts.master")

@section("content")

    <form action="" method="post">

        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                <h2><?php echo trans("users::users.edit") ?></h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo route("admin"); ?>"><?php echo trans("admin::common.admin") ?></a>
                    </li>
                    <li>
                        <a href="<?php echo route("admin.users.show"); ?>"><?php echo trans("users::users.users") ?></a>
                    </li>
                    <li class="active">
                        <strong><?php echo trans("users::users.edit") ?></strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 text-right">

                <?php if (Gate::allows("users.create")) { ?>
                <a href="<?php echo route("admin.users.create"); ?>" class="btn btn-primary btn-labeled btn-main">
                    <span class="btn-label icon fa fa-plus"></span>  <?php echo trans("users::users.add_new") ?>
                </a>
                <?php } ?>

                <button type="submit" class="btn btn-flat btn-danger btn-main">
                    <i class="fa fa-download" aria-hidden="true"></i>
                    <?php echo trans("users::users.save_user") ?>
                </button>

            </div>
        </div>

        <div class="wrapper wrapper-content fadeInRight">

            @include("admin::partials.messages")

            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">

                        <div class="panel-body">

                            <div class="form-group input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                <input name="username" value="<?php echo @Request::old("username", $user->username); ?>"
                                       class="form-control input-lg"
                                       placeholder="<?php echo trans("users::users.username") ?>"/>
                            </div>

                            <div class="form-group input-group">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <input name="password" autocomplete="off" value="" class="form-control input-lg"
                                       placeholder="<?php echo trans("users::users.password") ?>" type="password"/>
                            </div>

                            <div class="form-group input-group">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <input name="repassword" autocomplete="off" value="" class="form-control input-lg"
                                       placeholder="<?php echo trans("users::users.confirm_password") ?>"
                                       type="password"/>
                            </div>

                            <div class="form-group input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                <input name="email" value="<?php echo @Request::old("email", $user->email); ?>"
                                       class="form-control input-lg"
                                       placeholder="<?php echo trans("users::users.email") ?>"
                                       type="email"/>
                            </div>

                            <div class="row">
                                <div class="col-lg-3 col-md-3 text-center">

                                    <div class="row">

                                        <input type="hidden" value="<?php if ($user and $user->photo) {
                                            echo $user->photo->id;
                                        } else {
                                            echo 0;
                                        } ?>" id="user_photo_id" name="photo_id"/>

                                        <img class="col-lg-12" id="user_photo" style="width: 100%"
                                             src="<?php if ($user and $user->photo) { ?> <?php echo thumbnail($user->photo->path); ?> <?php } else { ?> <?php echo assets("admin::images/user.png"); ?><?php } ?>"/>

                                        <a href="javascript:void(0)"
                                           <?php if (($user and $user->photo_id != 0)){ ?>style="display: none"
                                           <?php } ?>
                                           id="change_photo"
                                           class="col-lg-12 image-label"><?php echo trans("users::users.change") ?></a>

                                        <a href="javascript:void(0)"
                                           <?php if (!$user or ($user and $user->photo_id == 0)){ ?>style="display: none"
                                           <?php } ?>
                                           id="remove_photo"
                                           class="col-lg-12 image-label"><?php echo trans("users::users.remove_photo") ?></a>
                                    </div>

                                </div>
                                <div class="col-lg-9 col-md-9">
                                    <div class="form-group">
                                        <input name="first_name"
                                               value="<?php echo @Request::old("first_name", $user->first_name); ?>"
                                               class="form-control input-lg"
                                               placeholder="<?php echo trans("users::users.first_name") ?>"/>
                                    </div>

                                    <div class="form-group">
                                        <input name="last_name"
                                               value="<?php echo @Request::old("last_name", $user->last_name); ?>"
                                               class="form-control input-lg"
                                               placeholder="<?php echo trans("users::users.last_name") ?>"/>
                                    </div>

                                </div>
                            </div>

                            <br/>
                            <div class="form-group">
                        <textarea name="about" class="markdown form-control"
                                  placeholder="<?php echo trans("users::users.about_me") ?>"
                                  rows="7"><?php echo @Request::old("about", $user->about); ?></textarea>
                            </div>

                            <?php Action::render("user.form.featured", $user); ?>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">

                        <div class="panel-body">

                            <?php if (Auth::user()->hasRole("superadmin")) { ?>
                            <div class="row form-group">
                                <label class="col-sm-3 control-label"><?php echo trans("users::users.role") ?></label>
                                <div class="col-sm-9">
                                    <select class="form-control select2 chosen-rtl" name="role_id">
                                        <?php foreach ($roles as $role) { ?>
                                        <option
                                            <?php if ($user and $user->role_id == $role->id) { ?> selected="selected"
                                            <?php } ?>
                                            value="<?php echo $role->id ?>"><?php echo $role->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label
                                    class="col-sm-3 control-label"><?php echo trans("users::users.activation") ?></label>
                                <div class="col-sm-9">
                                    <select class="form-control select2 chosen-rtl" name="status">
                                        <option
                                            value="1"
                                            <?php if ($user and $user->status == 1) { ?> selected="selected" <?php } ?>><?php echo trans("users::users.activated") ?></option>
                                        <option
                                            value="0"
                                            <?php if ($user and $user->status == 0) { ?> selected="selected" <?php } ?>><?php echo trans("users::users.deactivated") ?></option>
                                    </select>
                                </div>
                            </div>
                            <?php } else { ?>
                            <input type="hidden" name="role_id"
                                   value="<?php echo isset($user->id) ? $user->id : 0; ?>"/>
                            <input type="hidden" name="status"
                                   value="<?php echo isset($user->status) ? $user->status : 0; ?>"/>
                            <?php } ?>

                            <div class="row form-group">
                                <label
                                    class="col-sm-3 control-label"><?php echo trans("users::users.language") ?></label>
                                <div class="col-sm-9">
                                    <select class="form-control select2 chosen-rtl" name="lang">
                                        <?php foreach (Config::get("admin.locales") as $code => $lang) { ?>
                                        <option <?php if ($user and $code == $user->lang) { ?> selected="selected"
                                                <?php } ?>
                                                value="<?php echo $code; ?>"><?php echo $lang["title"]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row form-group">
                                <label class="col-sm-3 control-label"><?php echo trans("users::users.color") ?></label>
                                <div class="col-sm-9">
                                    <select class="form-control select2 chosen-rtl" name="color">
                                        <?php foreach (["blue", "green"] as $color) { ?>
                                        <option <?php if ($user and $color == $user->color) { ?> selected="selected"
                                                <?php } ?>
                                                value="<?php echo $color; ?>"><?php echo trans("users::users.color_" . $color) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <?php Action::render("user.form.side", $user); ?>

                        </div>
                    </div>

                    <div class="panel panel-default">

                        <div class="panel-body">

                            <div class="form-group input-group">
                                <span class="input-group-addon"><i class="fa fa-facebook"></i></span>
                                <input name="facebook" value="<?php echo @Request::old("facebook", $user->facebook); ?>"
                                       class="form-control input-lg"
                                       placeholder="<?php echo trans("users::users.facebook") ?>"/>
                            </div>

                            <div class="form-group input-group">
                                <span class="input-group-addon"><i class="fa fa-twitter "></i></span>
                                <input name="twitter" value="<?php echo @Request::old("twitter", $user->twitter); ?>"
                                       class="form-control input-lg"
                                       placeholder="<?php echo trans("users::users.twitter") ?>"/>
                            </div>

                            <div class="form-group input-group">
                                <span class="input-group-addon"><i class="fa fa-google-plus"></i></span>
                                <input name="google_plus"
                                       value="<?php echo @Request::old("google_plus", $user->google_plus); ?>"
                                       class="form-control input-lg"
                                       placeholder="<?php echo trans("users::users.googleplus") ?>"/>
                            </div>

                            <div class="form-group input-group">
                                <span class="input-group-addon"><i class="fa fa-linkedin"></i></span>
                                <input name="linked_in"
                                       value="<?php echo @Request::old("linked_in", $user->linked_in); ?>"
                                       class="form-control input-lg"
                                       placeholder="<?php echo trans("users::users.linkedin") ?>"/>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </form>

@stop

@push("header")

    <style>

        .image-label {
            margin-top: -24px;
        }

    </style>

@endpush

@push("footer")

    <script>
        $(document).ready(function () {

            $("#change_photo").filemanager({
                panel: "media",
                types: "png|jpg|jpeg|gif|bmp|image",
                done: function (result, base) {
                    if (result.length) {
                        var file = result[0];
                        $("#user_photo_id").val(file.id);
                        $("#user_photo").attr("src", file.thumbnail);
                    }

                    $("#change_photo").hide();
                    $("#remove_photo").show();
                },
                error: function (media_path) {
                    alert(media_path + " <?php echo trans("users::users.is_not_an_image") ?>");
                }
            });

            $("#remove_photo").click(function () {

                $("#user_photo_id").val(0);
                $("#user_photo").attr("src", "<?php echo assets("admin::images/user.png"); ?>");

                $("#remove_photo").hide();
                $("#change_photo").show();

                return false;
            });
        });

    </script>

@endpush
