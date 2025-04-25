$(document).ready(function() {

    var elementIds = [
        'fee_type',
    ];

    for (var index = 0; index < elementIds.length; ++index) {

        var elementId = elementIds[index];
        var $element = $('#' + elementId);

        if ($element.length && $element.prop("tagName").toLowerCase() === 'select') {
            $element.select2();
        }

    }

});


function showProcessingNotification() {

    $.toast({
        heading: '<h5 class="text-white"><i class="mdi mdi-spin mdi-loading mr-2"></i>Checking...</h5>',
        text: '<h5 class="mt-2 text-white">Processing Your Request</h5>',
        // icon: 'success',
        bgColor: '#E67E22',
        showHideTransition: 'slide',
        allowToastClose: false,
        //hideAfter: 10000,
        hideAfter: false,
        //autoHide: true,
        //loader: true,
        position: 'top-right',
    });

}


function showSuccessNotification(message) {

    swal.close();
    $.toast().reset('all');

    $.toast({
        heading: '<h5 class="text-white">Done!</h5>',
        text: '<h5 class="mt-2 text-white">'+message+'</h5>',
        icon: 'success',
        showHideTransition: 'slide',
        //bgColor: 'green',
        //textColor: '#eee',
        allowToastClose: false,
        hideAfter: 3000,
        loader: true,
        position: 'top-right',
    });

}


function showWarningNotification(message) {

    swal.close();
    $.toast().reset('all');

    $.toast({
        heading: '<h5 class="text-white">Warning!</h5>',
        text: '<h5 class="mt-2 text-white">'+message+'</h5>',
        icon: 'warning',
        showHideTransition: 'slide',
        //bgColor: 'green',
        //textColor: '#eee',
        allowToastClose: false,
        hideAfter: 3000,
        loader: true,
        position: 'top-right',
    });

}


function showErrorNotification(message) {

    swal.close();
    $.toast().reset('all');

    message = typeof message !== 'undefined' ? message : 'Something Went Wrong!';

    $.toast({
        heading: '<h5 class="text-white">Error!</h5>',
        text: '<h5 class="mt-2 text-white">'+message+'</h5>',
        icon: 'error',
        showHideTransition: 'slide',
        //bgColor: 'green',
        //textColor: '#eee',
        allowToastClose: false,
        hideAfter: 3000,
        loader: true,
        position: 'top-right',
    });

}


function reloadCurrentPage() {
    window.location = window.location.pathname;
}


function redirect(url, time) {

    setTimeout(function(){
        window.location.href = url;
    }, time);

}


function randomString(length) {

    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";

    for (var i = 0; i < length; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }

    return text ;
}