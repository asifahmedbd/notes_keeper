function scanDirectory() {

    $.ajax({
        url: '/directory-scanner',
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