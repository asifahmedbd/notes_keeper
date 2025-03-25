$(document).ready(function() {

    $("#loadDemoDataButton").click(function(){

        var company_id = $('#company_id').val();

        var params = {
            company_id: company_id,
        };

        showProcessingNotification();

        $.ajax({
            url: '/load/demo-data',
            type: 'POST',
            format: 'JSON',
            data: {"_token": $('#token').val(), params: params},

            success: function (response) {
                if (response.status === 'success') {
                    showSuccessNotification('Successfully Demo Data Has Been Generated');
                }
            },
            error: function (error) {
                showErrorNotification();
            }
        });


    });

});



