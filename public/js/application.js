var start_time = Date.now();
var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
var sections = [];
var subjects = [];
var students = [];
var toggle = false;

window.onload = function(){
    $('#render_time').text(((Date.now()) - start_time)/1000.0 + 's');
};

$(document).ready(function() {

    currentTime();

    var elementIds = [
        'fee_type',
        'edit_fee_type',
        'subject_type',
        'edit_subject_type',
        'class_id',
        'class_test_aggregation_method',
        'edit_class_id',
        'section_id',
        'class_teacher_id',
        'gender',
        'category_id',
        'blood_group',
        'religion',
        'payment_mode',
        'head_id',
        'created_by',
        'teacher_id',
        'edit_teacher_id',
        'session_id',
        'exam_id',
        'subject_id',
        'student_id',
        'sibling_student_id',
        'company_id',
        'employee_id',
        'leave_type_id',
        'leave_status',
        'edit_employee_id',
        'edit_leave_type_id',
        'edit_leave_status',
        'search_employee_id',
        'search_leave_type_id',
        'search_leave_status',
        'search_role',
        'attendance_status',
        'role',
        'department_id',
        'designation_id',
        'marital_status',
        'blood_group',
        'religion',
        'job_type',
        'salary_payment_mode',
        'pay_frequency',
        'reporting_manager',
        'discount_id',
        'type_id',
        'admission_status',
        'online_admission',
        'result_status',
        'alumni_session_id',
        'alumni_class_id',
        'alumni_section_id',
        'item_id',
        'supplier_id',
        'store_id',
        'default_store_id',
        'edit_default_store_id',
        'issued_to',
    ];

    for (var index = 0; index < elementIds.length; ++index) {

        var elementId = elementIds[index];
        var $element = $('#' + elementId);

        if ($element.length && $element.prop("tagName").toLowerCase() === 'select') {
            $element.select2();
        }

    }

    $('#company_name_span').click(function(){

        var classNameArray = [
            'bounceInDown animated',
            'tada animated',
            'rubberBand animated',
            'swing animated',
            'hinge animated',
        ];

        var className = 'hinge animated';

        $('#company_name_span').addClass(className);

        setTimeout(function(){
            $('#company_name_span').removeClass(className);
        }, 2000);

    });


    $('#clock_span').click(function(){

        var className = 'headShake animated';

        $('#clock_span').addClass(className);

        setTimeout(function(){
            $('#clock_span').removeClass(className);
        }, 2000);

    });


    $('#select_en').click(function(){

        var params = {
            locale: 'en'
        };

        $.ajax({
            url: '/change/lang',
            type: 'POST',
            format: 'JSON',
            data: {'_token': $('#token').val(), params: params},

            success: function(response) {
                reloadCurrentPage();
            },
            error: function(error) {
                showErrorNotification();
            }

        });
    });


    $('#select_bn').click(function(){

        var params = {
            locale: 'bn'
        };

        $.ajax({
            url: '/change/lang',
            type: 'POST',
            format: 'JSON',
            data: {'_token': $('#token').val(), params: params},

            success: function(response) {
                reloadCurrentPage();
            },
            error: function(error) {
                showErrorNotification();
            }

        });
    });


    $('#class_id').change(function () {

        var class_id = parseInt($("#class_id").val());

        if (class_id > 0) {
            if($('#section_id').length && $('#section_id').prop("tagName").toLowerCase() === 'select') renderSectionDropDown(class_id);
            if($('#subject_id').length && $('#subject_id').prop("tagName").toLowerCase() === 'select') renderSubjectDropDown(class_id);
            if($('#student_id').length && $('#student_id').prop("tagName").toLowerCase() === 'select') renderStudentDropDown(class_id);
        }

    });


    $('#section_id').change(function () {

        var class_id = parseInt($("#class_id").val());
        var section_id = parseInt($("#section_id").val());

        if (section_id > 0) {

            if($('#student_id').length && $('#student_id').prop("tagName").toLowerCase() === 'select') renderStudentDropDown(class_id);
        }

    });

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


function formatDate(inputDate) {
    var dateArray = inputDate.split('-');
    return (dateArray[1] + '/' + dateArray[2] + '/' + dateArray[0]);
}


function beautifyDate(date) {
    var d = new Date(date),
        month = months[d.getMonth()],
        day = '' + d.getDate(),
        year = d.getFullYear();

    //if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return month + ' ' + day + ', ' + year;
}


function beautifyDateTime(date) {

    var d = new Date(date),
        months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        month = months[d.getMonth()],
        day = d.getDate(),
        year = d.getFullYear(),
        hours = d.getHours(),
        minutes = d.getMinutes(),
        ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;

    var daySuffix;
    if (day % 10 == 1 && day != 11) {
        daySuffix = 'st';
    } else if (day % 10 == 2 && day != 12) {
        daySuffix = 'nd';
    } else if (day % 10 == 3 && day != 13) {
        daySuffix = 'rd';
    } else {
        daySuffix = 'th';
    }

    return day + daySuffix + ' ' + month + ' ' + year + ' ' + hours + ':' + minutes + ' ' + ampm;
}


function redirect(url, time) {

    setTimeout(function(){
        window.location.href = url;
    }, time);

}


function currentTime() {
    var date = new Date();

    var this_date = date.getDate();
    var month = months[date.getMonth()];
    var year = date.getFullYear();

    var hour = date.getHours();
    var min = date.getMinutes();
    var sec = date.getSeconds();
    hour = updateTime(hour);
    min = updateTime(min);
    sec = updateTime(sec);
    document.getElementById("clock").innerText = this_date + ' ' + month + ' ' + year + ' ' + hour + " : " + min + " : " + sec;
    var t = setTimeout(function(){ currentTime() }, 1000);
}


function updateTime(k) {
    if (k < 10) {
        return "0" + k;
    }
    else {
        return k;
    }
}


function viewSelectedImage(input, target) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $("#" + target).attr('src', e.target.result).height(200);
            $("#" + target).css({"display":"block"});
        };

        reader.readAsDataURL(input.files[0]);
    }
}


function pushToClassSectionArray(sections) {

    var section = {};

    for (var key in sections) {
        if (sections.hasOwnProperty(key)) {
            section[key] = sections[key];
        }
    }


    this.sections.push(section);

}


function pushToClassSubjectArray(subjects) {

    var subject = {};

    for (var key in subjects) {
        if (subjects.hasOwnProperty(key)) {
            subject[key] = subjects[key];
        }
    }


    this.subjects.push(subject);

}


function pushToStudentArray(student) {

    var studentObject = {};

    for (var key in student) {
        if (student.hasOwnProperty(key)) {
            studentObject[key] = student[key];
        }
    }


    this.students.push(studentObject);

}


function renderSectionDropDown(class_id) {

    var html_str = '<option value="0">Select Section</option>';

    for (var i=0; i<sections.length; i++) {

        var section = sections[i];

        if (class_id == section.class_id) {

            var section_id = section.section_id;
            var section_name = section.section_name;

            html_str += '<option value="'+ section_id+'">'+ section_name +'</option>';

        }
    }

    $('#section_id').empty().append(html_str);

}


function renderSubjectDropDown(class_id) {

    var html_str = '<option value="0">Select Subject</option>';

    for (var i=0; i<subjects.length; i++) {

        var subject = subjects[i];

        if (class_id == subject.class_id) {

            var subject_id = subject.subject_id;
            var subject_name = subject.subject_name;
            var subject_code = subject.subject_code != null ? '(' + subject.subject_code + ')' : '';

            html_str += '<option value="'+ subject_id+'">'+ subject_name + ' ' + subject_code + '</option>';

        }
    }

    $('#subject_id').empty().append(html_str);

}


function renderStudentDropDown(class_id) {

    const section_id = $('#section_id').length && $('#section_id').is('select') ? parseInt($('#section_id').val()) : 0;

    const filteredStudents = students.filter(student =>
        student.class_id === class_id && (section_id === 0 || student.section_id === section_id)
    );

    const options = filteredStudents.map(student =>
        `<option value="${student.student_id}">${student.student_name}</option>`
    );

    $('#student_id').html('<option value="0">All Students</option>' + options.join(''));
}


function getUserName() {
    return $('#user_name').val();
}


function camelize(input, separator) {

    if (typeof separator === 'undefined') separator = '_';

    return input.split(separator).map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ');
}


function getCurrentTime() {
    var time = new Date();
    return time.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
}


function convertToSlug(text) {
    return text.toLowerCase().replace(/\s+/g, '-');
}


function uc_first(string) {
    return string.substr(0,1).toUpperCase()+string.substr(1);
}


function printContent() {

    $('#print-content').removeClass('col-md-9').addClass('col-md-12');
    window.print();
    $('#print-content').removeClass('col-md-12').addClass('col-md-9');

}


function updateTotalMarks(state) {

    if (typeof state === 'undefined') state = '';

    var exam_marks = parseInt($('#'+ state +'exam_marks').val() != '' ? $('#'+ state +'exam_marks').val():0);
    var class_test_marks = parseInt($('#'+ state +'class_test_marks').val() != '' ? $('#'+ state +'class_test_marks').val():0);
    var teacher_discretion = parseInt($('#'+ state +'teacher_discretion').val() != '' ? $('#'+ state +'teacher_discretion').val():0);

    $('#'+ state +'total_marks').val((exam_marks + class_test_marks + teacher_discretion));

}


function toggleCheckbox(checkboxName) {
    this.toggle = !this.toggle;
    $('input[name="' + checkboxName + '"]').prop('checked', this.toggle);
}


function enableButton(checkboxName, buttonId) {

    var count = 0;

    $('input[name="'+ checkboxName +'"]').each(function() {
        var checkbox = $(this);
        if (checkbox.prop('checked')) count++;
    });

    if (count == 0) $('#' + buttonId).prop("disabled", true);
    else $('#' + buttonId).prop("disabled", false);

}


function getCurrentDate() {

    var now = new Date();
    var thisMonth = months[now.getMonth()];
    var date = now.getDate();
    var year = now.getFullYear();

    var today = thisMonth + ' ' + date + ', ' + year;
    return today;
}


function calculatePercentage(part, total) {
    if (total === 0) {
        console.error("Total cannot be zero.");
        return 0;
    }
    return ((part / total) * 100).toFixed(2);;
}
