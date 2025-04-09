var app_path = '';
var categories = [];
var expandedNodeKey = null;

$(document).ready(function () {

    app_path = $('#app_path').val();


    initializeFancyTree();


    $.getScript("https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@36.0.1/build/ckeditor.js", function() {

        ClassicEditor
            .create(document.querySelector('#document_text'))
            .then(function(editor) {
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
            })
            .catch(function(error) {
                console.error('Error initializing CKEditor:', error);
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
                        //pushToCategoryArray(response.category);
                        loadAndRenderFancyTree();
                    }
                },
                error: function () {
                    alert("Something went wrong with the creation of the category.");
                }
            });
        }
    });

});


function openCategorySelector() {
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


function initializeFancyTree() {

    $.getScript("https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.38.2/jquery.fancytree-all-deps.min.js")
        .done(function() {
            loadAndRenderFancyTree();
        })
        .fail(function(jqxhr, settings, exception) {
            console.error("Failed to load FancyTree:", exception);
        });
}


function loadAndRenderFancyTree() {
    $.ajax({
        url: app_path + '/get/category-structure',
        method: 'GET',
        cache: false,
        success: function (response) {
            if (response.status === 'success') {
                renderFancyTree(response.fancyTree);
            }
            else {
                console.error("Failed to load category tree.");
            }
        },
        error: function () {
            console.error("AJAX error while fetching category structure.");
        }
    });
}


function renderFancyTree(fancyTreeData) {

    var $tree = $("#fancytree_category_selector");
    var treeInstance = $.ui.fancytree.getTree($tree);

    if (treeInstance) {
        var expandedNode = treeInstance.getActiveNode();
        if (expandedNode) {
            expandedNodeKey = expandedNode.key;
        }
    }

    if (treeInstance) {
        treeInstance.destroy();
    }

    $tree.fancytree({
        source: fancyTreeData,
        click: function(event, data) {
            var node = data.node;
            if (!node.folder) return;

            $("#current_category_name").val(node.title);
            $("#category_id").val(node.key);
            $("#current_category_id").val(node.key);
            $(".current_category_name").text(node.title);
        },

        init: function(event, data) {
            if (expandedNodeKey) {
                var node = data.tree.getNodeByKey(expandedNodeKey);
                if (node) {
                    node.setExpanded(true);
                    node.setActive();
                }
            }
        }
    });
}