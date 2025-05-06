var app_path = '';

$(document).ready(function () {

    app_path = $('#app_path').val();

    $('#update_permission_button').click(function () {

        var role_name = $('#role_name').val();

        var permissions = [];

        $('input[type="checkbox"]').each(function () {
            if ($(this).is(':checked')) {
                permissions.push($(this).attr('id'));
            }
        });

        var params = {
            role_name: role_name,
            permissions: permissions
        };

        $.ajax({
            url: app_path + '/role-permission',
            type: 'POST',
            dataType: 'json',
            data: {params: params, "_token": $('#token').val()},

            success: function (response) {

                if (response.status === 'success') {
                    showSuccessNotification(response.message);
                }
                else {
                    showErrorNotification(response.message);
                }
            },
            error: function () {
                showErrorNotification("Something went wrong while updating permissions.");
            }
        });
    });

});


function updatePermission(role_name) {

    $('#update-permission-modal-title').text('Update Role Permission For ' + uc_first(role_name));
    $('#role_name').val(role_name);

    $('input[type="checkbox"]').prop('checked', false);

    $.ajax({
        url: app_path + '/role-permission/' + role_name + '/permissions',
        type: 'GET',
        dataType: 'json',
        success: function (response) {

            if (response.status === 'success') {

                var permissions = response.permissions;

                permissions.forEach(function (perm) {
                    $('#' + perm).prop('checked', true);
                });

                $('#update_permission_modal').modal('show');

            }

            else showErrorNotification('Failed to load permissions.');

        },
        error: function () {
            showErrorNotification('Error loading permissions.');
        }
    });
}