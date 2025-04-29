@extends('layouts.app')

@section('title', 'Role Permission')

@section('content')

    <script src="{{ env('APP_PATH') }}/js/access.js"></script>

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box p-2">
                <div class="row">

                    <div class="col-md-12">

                        <div class="text-right">
                            <button type="button" class="btn btn-dark waves-effect waves-light" data-toggle="modal" data-target="#add_role_modal">
                                <i class="md md-add"></i> Add New Role
                            </button>
                        </div>

                        <div class="">
                            <table class="table table-sm table-bordered table-striped table-hover mt-2">

                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th class="text-center" style="width: 14%">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($roles as $role)
                                    <tr id="role_{{ $role->id }}">
                                        <th scope="row">{{ $loop->index+1 }}</th>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ formatDate($role->created_at) }}</td>
                                        <td>
                                            <button class="btn btn-xs btn-icon waves-effect waves-light btn-warning" onclick="updatePermission('{{ $role->name }}')"> <i class="mdi mdi-lock-open"></i> </button>
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

    <div id="add_role_modal" class="modal bounceInDown animated">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Add New Role</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name" class="control-label">Name</label>
                                <input type="text" class="form-control" id="name" autocomplete="off">
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-blue waves-effect waves-light" data-dismiss="modal" id="add_role_button">Save</button>
                </div>

            </div>
        </div>
    </div>

    <div id="update_permission_modal" class="modal bounceInDown animated">
        <div class="modal-dialog modal-full">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="update-permission-modal-title">Update Role Permission</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="role_name" value="">

                    <div class="row">

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>View</th>
                                    <th>Create</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($components as $component)

                                    <tr>
                                        <th>{{ ucfirst($component) }}</th>
                                        <td>
                                            <div class="checkbox checkbox-success mb-2">
                                                <input id="view_{{ $component }}" type="checkbox">
                                                <label for="view_{{ $component }}">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="checkbox checkbox-success mb-2">
                                                <input id="create_{{ $component }}" type="checkbox">
                                                <label for="create_{{ $component }}">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="checkbox checkbox-success mb-2">
                                                <input id="edit_{{ $component }}" type="checkbox">
                                                <label for="edit_{{ $component }}">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="checkbox checkbox-success mb-2">
                                                <input id="delete_{{ $component }}" type="checkbox">
                                                <label for="delete_{{ $component }}">&nbsp;</label>
                                            </div>
                                        </td>
                                    </tr>

                                @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-blue waves-effect waves-light" data-dismiss="modal" id="update_permission_button">Save</button>
                </div>

            </div>
        </div>
    </div>

@endsection