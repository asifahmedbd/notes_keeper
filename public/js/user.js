var app_path = '';


$(document).ready(function () {

    app_path = $('#app_path').val();


    $('#add_user_button').click(function () {

        var name = $('#name').val();
        var email = $('#email').val();
        var password = $('#password').val();
        var role = $('#role').val();
        var user_email = $('#user_email').val();

        var params = {
            name: name,
            email: email,
            password: password,
            role: role,
            user_email: user_email,
        };

        $.ajax({

            url: app_path + '/add/user',
            type: 'POST',
            format: 'JSON',
            data: {'_token': $('#token').val(), params: params},

            success: function (response) {
                showSuccessNotification('User Has Been Created');
                reloadCurrentPage();
            },
            error: function (error) {
                showErrorNotification();
            }

        });

    });


    $('#edit_user_button').click(function () {

        var id = $('#user_id').val();
        var name = $('#edit_name').val();
        var role = $('#edit_role').val();

        var params = {
            id: id,
            name: name,
            role: role,
        };

        $.ajax({

            url: app_path + '/edit/user',
            type: 'POST',
            format: 'JSON',
            data: {'_token': $('#token').val(), params: params},

            success: function (response) {
                showSuccessNotification('User Has Been Edited');
                reloadCurrentPage();
            },
            error: function (error) {
                showErrorNotification();
            }

        });

    });


    $('#change_password_button').click(function () {

        var id = $('#user_id').val();
        var password = $('#new_password').val();

        var params = {
            id: id,
            password: password,
        };

        $.ajax({

            url: app_path + '/update/user-password',
            type: 'POST',
            format: 'JSON',
            data: {'_token': $('#token').val(), params: params},

            success: function (response) {
                showSuccessNotification('User Password Has Been Updated');
            },
            error: function (error) {
                showErrorNotification();
            }

        });

    });


});


function editUser(id, name, role) {

    $('#user_id').val(id);
    $('#edit_name').val(name);
    $('#edit_role').val(role);

    $('#edit_user_modal').modal('show');
}


function deleteUser(id) {

    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-success',
        buttonsStyling: false
    }).then(function () {

        var params = {
            id: id
        };

        $.ajax({
            url: app_path + '/delete/user',
            type: 'POST',
            format: 'JSON',
            data: {params: params, "_token": $('#token').val()},

            success: function (response) {
                $("#user_" + id).remove();
                showSuccessNotification('User Has Been Deleted!');
            },
            error: function (error) {
                showErrorNotification();
            }
        });

    });
}


function changePassword(id) {

    $('#user_id').val(id);
    $('#new_password').val(randomString(12));

    $('#change_password_modal').modal('show');
}
