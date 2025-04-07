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

<!-- <div style="overflow: hidden;width: 800px; ">
    <div id="#" style="width: 100%; height:550px; overflow: auto;"></div>
</div> -->

<!-- Modal for Viewing Files -->
<div class="modal fade" id="viewFileModal" tabindex="-1" aria-labelledby="viewFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">File Viewer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <div class="modal-body" style="overflow: hidden;">
                <div id="resolte-contaniner" style="width: 100%; height: 550px; overflow: auto;"></div>
            </div>
        </div>
    </div>
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
                                                    <button class="btn btn-primary btn-xs view-file-btn demos" data-file="demo.pdf" data-file-path="${file.file_path}">
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

        function loadPptxViewer(file_path) {
            // Load the script only if not already loaded
            var app_path = $('#app_path').val();
            $.getScript(app_path + '/include/PPTXjs/js/pptxjs.js', function () {
                $.getScript(app_path + '/include/PPTXjs/js/divs2slides.js', function () {
                    renderPptx(file_path);
                });
            });

            // if (typeof $.fn.pptxToHtml === "undefined") {
            //     $.getScript('/include/PPTXjs/js/pptxjs.js', function () {
            //         $.getScript('/include/PPTXjs/js/divs2slides.js', function () {
            //             renderPptx(file_path);
            //         });
            //     });
            // } else {
            //     renderPptx(file_path);
            // }

            // function renderPptx(file_path) {
            //     $("#resolte-contaniner").html(""); // Clear previous content
            //     $("#resolte-contaniner").pptxToHtml({
            //         pptxFileUrl: app_path + file_path
            //     });
            // }

            function renderPptx(file_path) {
                const container = $("#resolte-contaniner");
                container.html("<p id='loading-msg'>Loading presentation...</p>");

                // Setup a timeout as a fallback
                const fallbackTimeout = setTimeout(() => {
                    if (container.find(".slide").length === 0) {
                        container.html("<p class='text-danger'>Could not display the presentation. Try another file.</p>");
                    }
                }, 3000); // You can adjust the time as needed

                // Load presentation
                container.pptxToHtml({
                    pptxFileUrl: app_path + file_path,
                    slideMode: true,
                    keyBoardShortCut: true,
                    slideModeConfig: {
                        toolbar: {
                            enabled: true,
                            autoHide: false
                        }
                    },
                    pptxFileConversionComplete: function () {
                        // Clear loading message
                        $("#loading-msg").remove();

                        // Cancel fallback if content loaded successfully
                        clearTimeout(fallbackTimeout);

                        // Run toolbar setup
                        if (typeof divs2slides === "function") {
                            divs2slides();
                        } else {
                            console.warn("divs2slides not defined.");
                        }
                    }
                });
            }





        }


        $(document).on("click", ".demos", function () {
            var app_path = $('#app_path').val();
            var file_path = $(this).data("file-path");
            var file_name = file_path.split('/').pop();
            var extension = file_name.split('.').pop().toLowerCase();

            $("#viewFileModal").modal("show");
            $("#resolte-contaniner").html("");

            if (extension === "pptx") {
                loadPptxViewer(file_path);
            } else {
                // Default to officeToHtml for others like .docx, .pdf, etc.
                if (typeof $.fn.officeToHtml === "undefined") {
                    $.getScript(app_path + '/js/officeToHtml.js', function () {
                        loadOfficeDoc(file_path);
                    });
                } else {
                    loadOfficeDoc(file_path);
                }
            }

            function loadOfficeDoc(file_path) {
                $("#resolte-contaniner").officeToHtml({
                    url: app_path + file_path,
                    pdfSetting: {
                        setLang: "",
                        setLangFilesPath: ""
                    }
                });
            }
        });


    });
</script>

@endsection
