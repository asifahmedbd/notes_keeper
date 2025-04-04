var app_path = '';

$(document).ready(function () {

    app_path = $('#app_path').val();

});


function scanDirectory() {

    $.ajax({
        url: app_path + '/directory-scanner',
        type: 'POST',
        format: 'JSON',
        data: {"_token": $('#token').val()},

        success: function (response) {

            if (response.status == 'success') {
                alert('Directory Scan Completed!');
            }

        },
        error: function (error) {
            showErrorNotification();
        }
    });

}