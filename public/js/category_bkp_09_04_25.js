var app_path = '';
var categories = [];

$(document).ready(function () {

    app_path = $('#app_path').val();

    $.getScript("https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@36.0.1/build/ckeditor.js", function () {
        ClassicEditor
            .create(document.querySelector('#document_text'))
            .then(editor => {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
                // Add custom upload file button
                const toolbarElement = editor.ui.view.toolbar.element;
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.style.display = 'none';

                var editingView = editor.editing.view;
                var viewDocument = editingView.document;

                editingView.change(function(writer) {
                    writer.setStyle('height', '300px', viewDocument.getRoot());
                });

                editor.ui.focusTracker.on('change:isFocused', function(evt, name, isFocused) {
                    if (isFocused) {
                        editingView.change(function(writer) {
                            writer.setStyle('height', '300px', viewDocument.getRoot());
                        });
                    }
                });
    
                const uploadButton = document.createElement('button');
                uploadButton.type = 'button';
                uploadButton.innerText = '📎 Upload File';
                uploadButton.style.marginLeft = '10px';
    
                toolbarElement.appendChild(uploadButton);
                toolbarElement.appendChild(fileInput);
    
                uploadButton.addEventListener('click', () => {
                    fileInput.click();
                });
    
                fileInput.addEventListener('change', () => {
                    const file = fileInput.files[0];
                    if (!file) return;
    
                    const formData = new FormData();
                    formData.append('upload', file);
                    formData.append('_token', csrfToken);
    
                    fetch('/upload-file', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.url) {
                            // Insert link to uploaded file
                            editor.model.change(writer => {
                                const insertPosition = editor.model.document.selection.getFirstPosition();
                                writer.insertText(file.name, { linkHref: data.url }, insertPosition);
                                //writer.insertText(`Download ${file.name}`, { linkHref: data.url }, insertPosition);

                            });
                        } else {
                            console.error("Upload failed:", data);
                            alert("Upload failed.");
                        }
                    })
                    .catch(error => {
                        console.error("Error uploading file:", error);
                        alert("An error occurred while uploading the file.");
                    });
                });
            })
            .catch(error => {
                console.error('CKEditor initialization failed:', error);
            });
    });
    
    


    $('#create_category_button').click(function () {

        var parent_id = $('#current_category_id').val();
        var category_name = $('#category_name').val();

        if (category_name !== '') {
            var params = {
                parent_id: parent_id,
                category_name: category_name,
            };

            $.ajax({
                url: app_path + '/create/category',
                type: 'POST',
                format: 'JSON',
                data: {params: params, "_token": $('#token').val()},
                success: function (response) {
                    if (response.status === 'success') {
                        alert(category_name + ' Created!');
                        $('#category_id').val(response.category_id);
                        $('#current_category_id').val(response.category_id);
                        $('#current_category_name').val(category_name);
                        $('#category_name').val('');
                        pushToCategoryArray(response.category);
                    }
                },
                error: function () {
                    alert("Something went wrong with the creation of the category.");
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
        $('#current_category_name').val('Root');
    }
    else {
        category = getCategory(parent_id);
        $('.current_category_name').text(category.category_name);
        $('#current_category_name').val(category.category_name);

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