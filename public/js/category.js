var categories = [];

$(document).ready(function() {

    $('#create_category_button').click(function () {

        var parent_id = $('#current_category_id').val();
        var category_name = $('#category_name').val();

        if (category_name != '') {

            var params = {
                parent_id: parent_id,
                category_name: category_name,
            };

            $.ajax({
                url: '/create/category',
                type: 'POST',
                format: 'JSON',
                data: {params: params, "_token": $('#token').val()},

                success: function (response) {

                    if (response.status == 'success') {
                        pushToCategoryArray(response.category);
                        $('#category_id').val(response.category_id);
                        $('#category_name').val('');
                        alert(category_name + ' Created!');
                    }

                },
                error: function (error) {
                    showErrorNotification();
                }
            });

        }

    });

});


function pushToCategoryArray(category) {

    var categoryObject = {};

    for (var key in category) {
        if (category.hasOwnProperty(key)) {
            categoryObject[key] = category[key];
        }
    }

    this.categories.push(categoryObject);

}


function openCategorySelector() {

    var parent_id = '0';
    renderCategoryTable(parent_id);

    $('#select_category_modal').modal('show');

}


function renderCategoryTable(parent_id) {

    var html_str = '';
    var counter = 1;
    var category = '';


    if (parseInt(parent_id) == 0) {
        $('.current_category_name').text('Root');
    }
    else {
        category = getCategory(parent_id);
        $('.current_category_name').text(category.category_name);

        html_str += '<tr>' +
            '<th onclick="renderCategoryTable('+ category.parent_id +');" colspan="3" class="pointer text-center text-warning">Return To Previous Category</th>' +
            '</tr>';
    }

    for (var i=0; i<categories.length; i++) {

        category = categories[i];

        if (parent_id == category.parent_id) {

            html_str += '<tr>' +
                '<th scope="row">'+ counter++ +'</th>' +
                '<td class="pointer" onclick="renderCategoryTable('+ category.category_id +');">'+ category.category_name +'</td>' +
                '<td>'+ category.parent_id +'</td>' +
                '</tr>';

        }

    }

    $('#current_category_id').val(parent_id);
    $('#category_table').empty().append(html_str);

}


function getCategory(category_id) {

    for (var i=0; i<categories.length; i++) {

        var category = categories[i];

        if (category_id == category.category_id) {
            return category;
        }

    }
    
}