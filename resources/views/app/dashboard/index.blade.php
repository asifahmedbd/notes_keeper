@extends('layouts.app')
@section('title', 'Dashboard')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

<style>
    table.fancytree-ext-table{
        font-size: 10pt !important;
    }
    .category-node span.fancytree-title {
        color: blue !important;
        font-weight: bold;
    }

    /* Red color for documents */
    .document-node span.fancytree-title {
        color: red !important;
        font-style: italic;
    }
</style>

<!-- Required CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.38.2/skin-win8/ui.fancytree.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.38.2/modules/jquery.fancytree.table.min.css" rel="stylesheet">

<!-- jQuery (Load First) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>

<!-- FancyTree JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.38.2/jquery.fancytree-all-deps.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.38.2/modules/jquery.fancytree.table.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/viewerjs@1.11.4/dist/viewer.min.css" />
<script src="https://cdn.jsdelivr.net/npm/viewerjs@1.11.4/dist/viewer.min.js"></script>


<!--PDF-->
<link rel="stylesheet" href="{{ asset('include/pdf/pdf.viewer.css') }}">
<script src="{{ asset('include/pdf/pdf.js') }}"></script>

<!--Docs-->
<script src="{{ asset('include/docx/jszip-utils.js') }}"></script>
<script src="{{ asset('include/docx/mammoth.browser.min.js') }}"></script>

<!--PPTX-->
<link rel="stylesheet" href="{{ asset('include/PPTXjs/css/pptxjs.css') }}">
<link rel="stylesheet" href="{{ asset('include/PPTXjs/css/nv.d3.min.css') }}">
<link rel="stylesheet" href="{{ asset('include/revealjs/reveal.css') }}">

<script type="text/javascript" src="{{ asset('include/PPTXjs/js/filereader.js') }}"></script>
<script type="text/javascript" src="{{ asset('include/PPTXjs/js/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('include/PPTXjs/js/nv.d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('include/PPTXjs/js/pptxjs.js') }}"></script>
<script type="text/javascript" src="{{ asset('include/PPTXjs/js/divs2slides.js') }}"></script>

<!--All Spreadsheet -->
<link rel="stylesheet" href="{{ asset('include/SheetJS/handsontable.full.min.css') }}">
<script type="text/javascript" src="{{ asset('include/SheetJS/handsontable.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('include/SheetJS/xlsx.full.min.js') }}"></script>

<!--Image viewer-->
<link rel="stylesheet" href="{{ asset('include/verySimpleImageViewer/css/jquery.verySimpleImageViewer.css') }}">
<script type="text/javascript" src="{{ asset('include/verySimpleImageViewer/js/jquery.verySimpleImageViewer.js') }}"></script>

<!-- officeToHtml -->
<script src="{{ asset('js/officeToHtml.js') }}?v={{ time() }}"></script>
<link rel="stylesheet" href="{{ asset('css/officeToHtml.css') }}">

<div class="row mb-3">
    <div class="col text-end">
        <a href="{{ route('memo.create') }}" class="btn btn-primary">New Memo</a>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="box">

            <table id="treetable">
                <thead>
                <tr>
                    <th>File Name</th>
                    <th>Uploaded By</th>
                    <th>Created On</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Detail section -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Memo Details</div>
            <div class="card-body" id="folder-details">
                Click on a folder to see details.
            </div>
        </div>
    </div>
</div>



<!-- Modal for File Details -->
<div class="modal fade" id="fileDetailsModal" tabindex="-1" aria-labelledby="fileDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileDetailsModalLabel">File Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="fileDetailsContent">
                <!-- File details will be loaded here -->
                Loading...
            </div>
        </div>
    </div>
</div>

<div style="overflow: hidden;width: 800px; ">
    <div id="resolte-contaniner" style="width: 100%; height:550px; overflow: auto;"></div>
</div>

<script>
    $(document).ready(function() {
        // Load FancyTree script first
        //var folderDetailsHtml = "<p>No folder details available.</p>";

        var app_path = $('#app_path').val();

        $.getScript("https://cdnjs.cloudflare.com/ajax/libs/jquery.fancytree/2.38.2/jquery.fancytree-all-deps.min.js", function() {
            console.log("FancyTree script loaded!");

            // Initialize FancyTree
            $("#treetable").fancytree({
                extensions: ["table", "glyph"],
                checkbox: false, // Set to true if you need checkboxes
                table: {
                    indentation: 20,      // indent 20px per node level
                    nodeColumnIdx: 0,     // render the node title into the first column
                },
                glyph: {
                    map: {
                        doc: "fa fa-file",
                        folder: "fa fa-folder",
                        folderOpen: "fa fa-folder-open"
                    }
                },
                source: {!! json_encode($directoryTree) !!},

                renderColumns: function(e, data) {
                    var node = data.node,
                        $tdList = $(node.tr).find(">td");

                    // Column 1 (index 0) is rendered by fancytree (node title)

                    // Column 2 - Uploaded By
                    $tdList.eq(1).text(node.data.uploaded_by || '-');

                    // Column 3 - Created On
                    $tdList.eq(2).text(node.data.memo_created_on || '-');
                },

                // Node click event for details fetching
                activate: function(event, data) {
                    let node = data.node;
                    //console.log(node);
                    if (node.folder === false) {
                        // Send AJAX request to fetch folder details
                        $.ajax({
                            url: app_path + "/folder-details", // Your route to fetch data
                            type: "GET",
                            data: { folderId: node.key },
                            success: function(response) {
                                console.log("Data fetched successfully:", response);

                                // Display the folder details
                                let folderDetailsHtml = `
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5>Folder: ${node.title}</h5>
                                        <button class="btn btn-sm btn-warning edit-memo-btn" data-folder-id="${node.key}">Edit Memo</button>
                                    </div>
                                    <p><strong>Date:</strong> ${response.folder_created_on ?? "N/A"}</p>
                                    <p><strong>Creator:</strong> ${response.folder_created_by ?? "Unknown"}</p>
                                    <p><strong>Description:</strong> ${response.description ?? "No description available"}</p>
                                `;

                                // Check if there are files in the response
                                if (response.folder_files && response.folder_files.length > 0) {
                                    folderDetailsHtml += `
                                        <h4>Files:</h4>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>File Name</th>
                                                    <th>Size</th>
                                                    <th>Type</th>
                                                    <th>Uploaded On</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                    `;

                                    // Loop through the files and add rows to the table
                                    response.folder_files.forEach((file, index) => {
                                        folderDetailsHtml += `
                                            <tr>
                                                <td>${index + 1}</td>
                                                <td>${file.file_name ?? "N/A"}</td>
                                                <td>${file.file_size ?? "N/A"}</td>
                                                <td>${file.file_type ?? "N/A"}</td>
                                                <td>${file.uploaded_on ?? "N/A"}</td>
                                                <td>
                                                    <button class="btn btn-primary btn-xs view-file-btn demos" data-file="demo.pptx" data-file-path="${file.file_path}">
                                                        View File
                                                    </button>
                                                    <button class="btn btn-primary btn-xs view-file-btn download" data-file="demo.pptx" data-file-path="${file.file_path}">
                                                        Download
                                                    </button>
                                                </td>
                                            </tr>
                                        `;
                                    });

                                    folderDetailsHtml += `
                                            </tbody>
                                        </table>
                                    `;

                                     // Add Document Information Section
                                    folderDetailsHtml += `
                                        <h4>Document Information:</h4>
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th>Status</th>
                                                    <td>${response.document_status ?? "Draft"}</td>
                                                </tr>
                                                <tr>
                                                    <th>Category</th>
                                                    <td>${response.document_category ?? "PDM Access Request"}</td>
                                                </tr>
                                                <tr>
                                                    <th>Keywords</th>
                                                    <td>${response.document_keywords ?? "PDM"}</td>
                                                </tr>
                                                <tr>
                                                    <th>Last Updated</th>
                                                    <td>${response.document_last_updated ?? "N/A"}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    `;

                                } else {
                                    folderDetailsHtml += `<p>No files available in this folder.</p>`;
                                }

                                // Update the folder details container
                                $("#folder-details").html(folderDetailsHtml);
                            },
                            error: function() {
                                console.error("Failed to fetch folder details.");
                                $("#folder-details").html("<p class='text-danger'>Failed to load folder details.</p>");
                            }
                        });
                    }
                }
            });

            console.log("FancyTree initialized successfully!");
        });


        // Handle "View File" button click
        // $(document).on("click", ".view-file-btn", function() {
        //     const filePath = $(this).data("file-path");
        //     $("#fileDetailsModal").modal("show");
        //     // Fetch file details using AJAX
        //     $.ajax({
        //         url: app_path + "{{ route('file.viewer') }}", // Route to fetch file details
        //         method: "GET",
        //         data: { filePath: filePath },
        //         success: function(response) {
        //             // Load the response into the modal
        //             $("#fileDetailsContent").html(response);
        //             // Show the modal
        //             //$("#fileDetailsModal").modal("show");
        //         },
        //         error: function() {
        //             alert("Failed to load file details.");
        //         }
        //     });
        // });

        $(document).on("click", ".demos", function() {
              //e.preventDefault();

              $(".sdb_holder li").removeClass("active");
              $(this).parent().addClass("active");
              var id = $(this).attr("id");
              $("#head-name").html($(this).html());
              $("#description").hide();
              $("#resolte-contaniner").html("");
              $("#resolte-contaniner").show();
              $("#resolte-text").show();
              if (id != "demo_input") {

                $("#select_file").hide();
                var file_path = "files\\" + $(this).data("file");
                $("#a_file").html($(this).data("file")).attr("href", file_path);
                $("#a_file").show();
                $("#file_p").show();

                $("#resolte-contaniner").officeToHtml({
                  url: app_path + file_path,
                  pdfSetting: {
                    setLang: "",
                    setLangFilesPath: "" /*"include/pdf/lang/locale" - relative to app path*/
                  }
                });
            } else {

                $("#select_file").show();
                $("#file_p").show();
                $("#a_file").hide();

                $("#resolte-contaniner").officeToHtml({
                  inputObjId: "select_file",
                  pdfSetting: {
                    setLang: "",
                    setLangFilesPath: "" /*"include/pdf/lang/locale" - relative to app path*/
                  }
                });
            }
        });

    });
</script>

@endsection
