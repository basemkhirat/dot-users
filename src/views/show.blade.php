@extends("admin::layouts.master")

@section("content")
    <div class="row wrapper border-bottom white-bg page-heading">

        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">

            <h2>
                <i class="fa fa-users"></i>
                <?php echo trans("users::users.users") ?>
            </h2>

            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo route("admin"); ?>"><?php echo trans("admin::common.admin") ?></a>
                </li>
                <li>
                    <a href="<?php echo route("admin.users.show"); ?>">
                        <?php echo trans("users::users.users") ?>
                        (<?php echo $users->total() ?>)</a>
                </li>
            </ol>

        </div>

        <div class="col-lg-8 col-md-6 col-sm-6 col-xs-12 text-right">
            <?php if (Gate::allows("users.create")) { ?>
            <a href="<?php echo route("admin.users.create"); ?>" class="btn btn-primary btn-labeled btn-main"> <span
                    class="btn-label icon fa fa-plus"></span> <?php echo trans("users::users.add_new") ?></a>
            <?php } ?>
        </div>

    </div>

    <div class="wrapper wrapper-content fadeInRight">

        <div id="content-wrapper">

            @include("admin::partials.messages")

            <form action="" method="get" class="filter-form">
                <input type="hidden" name="per_page" value="<?php echo Request::get('per_page') ?>"/>
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <select name="sort" class="form-control chosen-select chosen-rtl">
                                <option
                                    value="first_name"
                                    <?php if ($sort == "first_name") { ?> selected='selected' <?php } ?>><?php echo trans("users::users.attributes.first_name"); ?></option>
                                <option
                                    value="created_at"
                                    <?php if ($sort == "created_at") { ?> selected='selected' <?php } ?>><?php echo trans("users::users.attributes.created_at"); ?></option>
                            </select>
                            <select name="order" class="form-control chosen-select chosen-rtl ">
                                <option
                                    value="DESC"
                                    <?php if ($order == "DESC") { ?> selected='selected' <?php } ?>><?php echo trans("users::users.desc"); ?></option>
                                <option
                                    value="ASC"
                                    <?php if ($order == "ASC") { ?> selected='selected' <?php } ?>><?php echo trans("users::users.asc"); ?></option>
                            </select>
                            <button type="submit"
                                    class="btn btn-primary"><?php echo trans("users::users.order"); ?></button>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">

                            <select name="status" class="form-control chosen-select chosen-rtl">
                                <option value=""><?php echo trans("users::users.all"); ?></option>
                                <option <?php if (Request::get("status") == "1") { ?> selected='selected' <?php } ?>
                                value="1"><?php echo trans("users::users.activated"); ?></option>
                                <option <?php if (Request::get("status") == "0") { ?> selected='selected' <?php } ?>
                                value="0"><?php echo trans("users::users.deactivated"); ?></option>
                            </select>

                            <select name="role_id" class="form-control chosen-select chosen-rtl">
                                <option value=""><?php echo trans("users::users.all_roles"); ?></option>
                                <?php foreach ($roles as $role) { ?>
                                <option <?php if ($role->id == Request::get("role_id")) { ?> selected='selected'
                                        <?php } ?>
                                        value="<?php echo $role->id; ?>"><?php echo $role->name ?></option>
                                <?php } ?>
                            </select>

                            <button type="submit"
                                    class="btn btn-primary"><?php echo trans("users::users.filter"); ?></button>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4">
                        <form action="" method="get" class="search_form">
                            <div class="input-group">
                                <input name="q" value="<?php echo Request::get("q"); ?>" type="text"
                                       class=" form-control"
                                       placeholder="<?php echo trans("users::users.search_users") ?> ...">
                                <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                        </span>
                            </div>
                        </form>
                    </div>
                </div>
            </form>

            <form action="" method="post" class="action_form">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"/>
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5> <?php echo trans("users::users.users") ?> </h5>
                    </div>
                    <div class="ibox-content">

                        <?php if (count($users)) { ?>

                        <div class="row">
                            <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 action-box">
                                <select name="action" class="form-control pull-left">
                                    <option value="-1"
                                            selected="selected"><?php echo trans("users::users.bulk_actions"); ?></option>
                                    <option value="delete"><?php echo trans("users::users.delete"); ?></option>
                                </select>
                                <button type="submit"
                                        class="btn btn-primary pull-right"><?php echo trans("users::users.apply"); ?></button>
                            </div>

                            <div class="col-lg-6 col-md-4 hidden-sm hidden-xs"></div>

                            <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                                <select class="pull-left form-control per_page_filter">
                                    <option value="" selected="selected">
                                        -- <?php echo trans("users::users.per_page") ?> --
                                    </option>
                                    <?php foreach (array(10, 20, 30, 40) as $num) { ?>
                                    <option
                                        value="<?php echo $num; ?>"
                                        <?php if ($num == $per_page) { ?> selected="selected" <?php } ?>><?php echo $num; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                        </div>

                        <div class="table-responsive">

                            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-hover">
                                <thead>
                                <tr>

                                    <th style="width:35px"><input type="checkbox" class="i-checks check_all"
                                                                  name="ids[]"/>
                                    </th>
                                    <th style="width:50px"><?php echo trans("users::users.photo") ?></th>
                                    <th><?php echo trans("users::users.name"); ?></th>
                                    <th><?php echo trans("users::users.email"); ?></th>
                                    <th><?php echo trans("users::users.created"); ?></th>
                                    <th><?php echo trans("users::users.role"); ?></th>
                                    <th><?php echo trans("users::users.actions") ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 0;
                                foreach ($users as $user) { ?>
                                <tr>

                                    <td>
                                        <input type="checkbox" class="i-checks" name="id[]"
                                               value="<?php echo $user->id; ?>"/>
                                    </td>

                                    <td>
                                        <?php if ($user->photo) { ?>
                                        <img class="img-rounded" style="width:50px"
                                             src="<?php echo thumbnail($user->photo->path) ?>"/>
                                        <?php } else { ?>
                                        <img class="img-rounded"
                                             src="<?php echo assets("admin::images/user.png"); ?>"/>
                                        <?php } ?>
                                    </td>

                                    <td>

                                        <a class="text-navy"
                                           href="<?php echo URL::to(ADMIN) ?>/users/<?php echo $user->id; ?>/edit">
                                            <strong> <?php echo $user->name; ?> </strong>
                                        </a>

                                    </td>

                                    <td>
                                        <small>
                                            <?php if ($user->email == "") { ?>
                                            -
                                            <?php } else { ?>
                                                <?php echo $user->email; ?>
                                            <?php } ?>
                                        </small>
                                    </td>

                                    <td>
                                        <small>
                                            <?php echo $user->created_at->render(); ?>
                                        </small>
                                    </td>

                                    <td>
                                        <small>
                                            <?php if ($user->role) { ?>
                                                <?php echo $user->role->name; ?>
                                            <?php } else { ?>
                                            -
                                            <?php } ?>
                                        </small>
                                    </td>

                                    <td class="center">

                                        <a href="<?php echo URL::to(ADMIN) ?>/users/<?php echo $user->id; ?>/edit">
                                            <i class="fa fa-pencil text-navy"></i>
                                        </a>

                                        <a class="delete_user ask"
                                           message="<?php echo trans("users::users.sure_delete") ?>"
                                           href="<?php echo URL::route("admin.users.delete", array("id" => $user->id)) ?>">
                                            <i class="fa fa-times text-navy"></i>
                                        </a>

                                    </td>
                                </tr>
                                <?php $i++;
                                } ?>

                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 text-center">
                                <?php echo trans("users::users.page"); ?>
                                <?php echo $users->currentPage() ?>
                                <?php echo trans("users::users.of") ?>
                                <?php echo $users->lastPage() ?>
                            </div>
                            <div class="col-lg-12 text-center">
                                <?php echo $users->appends(Request::all())->render(); ?>
                            </div>

                        </div>

                        <?php } else { ?>
                    <?php echo trans("users::users.no_records"); ?>
                <?php } ?>

                    </div>
                </div>
            </form>

        </div>
    </div>

@stop

@push("footer")

    <script>

        $(document).ready(function () {

            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });

            $('.check_all').on('ifChecked', function (event) {
                $("input[type=checkbox]").each(function () {
                    $(this).iCheck('check');
                    $(this).change();
                });
            });

            $('.check_all').on('ifUnchecked', function (event) {
                $("input[type=checkbox]").each(function () {
                    $(this).iCheck('uncheck');
                    $(this).change();
                });
            });

            $('.delete_user').click(function (event) {
                var self = $(this);
                var user_id = $(this).attr('data-id');
                $("#current_user_id").val(user_id);
                $('#all_users_delete option').prop('disabled', false);
                $('#all_users_delete option[value=' + user_id + ']').prop('disabled', true);
            });

            $(".filter-form input[name=per_page]").val($(".per_page_filter").val());
            $(".per_page_filter").change(function () {
                var base = $(this);
                var per_page = base.val();
                $(".filter-form input[name=per_page]").val(per_page);
                $(".filter-form").submit();
            });

        });
    </script>

@endpush
