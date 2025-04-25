@extends('layouts.app')

@section('title', 'Users')

@section('content')

    <script src="{{ env('APP_PATH') }}/js/user.js"></script>

    <input type="hidden" id="user_id" value="0">

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box p-2">
                <div class="row">

                    <div class="col-md-12">

                        <div class="text-right">
                            <button type="button" class="btn btn-dark waves-effect waves-light" data-toggle="modal" data-target="#add_user_modal">
                                <i class="md md-add"></i> Add New User
                            </button>
                        </div>

                        <div class="">
                            <table class="table table-sm table-bordered table-striped table-hover mt-2">

                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th class="text-center" style="width: 14%">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($users as $user)
                                    <tr id="user_{{ $user->id }}">
                                        <th scope="row">{{ $loop->index+1 }}</th>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ camelize($user->role) }}</td>
                                        <td>{{ formatDate($user->created_at) }}</td>
                                        <td>
                                            <button class="btn btn-xs btn-icon waves-effect waves-light btn-warning" onclick="changePassword({{ $user->id }})"> <i class="mdi mdi-lock-open"></i> </button>
                                            <button class="btn btn-xs btn-icon waves-effect waves-light btn-blue" onclick="editUser('{{ $user->id }}', '{{ $user->name }}', '{{ $user->role }}')"> <i class="fa fa-edit"></i> </button>
                                            <button class="btn btn-xs btn-icon waves-effect waves-light btn-danger" onclick="deleteUser({{ $user->id }})"> <i class="mdi mdi-account-remove"></i> </button>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="add_user_modal" class="modal bounceInDown animated">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Add New User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="control-label">Name</label>
                                <input type="text" class="form-control" id="name" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="control-label">Username</label>
                                <input type="text" class="form-control" id="email" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="control-label">Password</label>
                                <input type="text" class="form-control" id="password" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role" class="control-label">Role</label>
                                <select id="role" class="form-control">
                                    <option value="reader">Reader</option>
                                    <option value="editor">Editor</option>
                                    <option value="admin">Admin</option>
                                </select>

                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-blue waves-effect waves-light" data-dismiss="modal" id="add_user_button">Save</button>
                </div>

            </div>
        </div>
    </div>

    <div id="edit_user_modal" class="modal bounceInDown animated">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Edit User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_name" class="control-label">Name</label>
                                <input type="text" class="form-control" id="edit_name" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_role" class="control-label">Role</label>
                                <select id="edit_role" class="form-control">
                                    <option value="reader">Reader</option>
                                    <option value="editor">Editor</option>
                                    <option value="admin">Admin</option>
                                </select>

                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-blue waves-effect waves-light" data-dismiss="modal" id="edit_user_button">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div id="change_password_modal" class="modal bounceInDown animated">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Change User Password</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="new_password" class="control-label">Password</label>
                                <input type="text" class="form-control" id="new_password" autocomplete="off">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-blue waves-effect waves-light" data-dismiss="modal" id="change_password_button">Save</button>
                </div>
            </div>
        </div>
    </div>

@endsection