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

        figure.table {
            overflow-x: auto;
            margin: 1em 0;
        }

        figure.table table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        figure.table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        /* Style first row like a header */
        figure.table tbody tr:first-child td {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        /* Zebra striping for better readability */
        figure.table tbody tr:not(:first-child):nth-child(even) td {
            background-color: #fafafa;
        }

        figure.table a {
            color: #007bff;
            text-decoration: none;
        }

        figure.table a:hover {
            text-decoration: underline;
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

    @if(auth()->user()->can('create_memo'))
        <div class="row mb-3">
            <div class="col text-end">
                <a href="{{ route('memo.create') }}" class="btn btn-primary">New Memo</a>
            </div>
        </div>
    @endif

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

    <!-- Modal for Viewing Video -->
    <div class="modal fade" id="viewVideoModal" tabindex="-1" aria-labelledby="viewVideoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Video Viewer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="overflow: hidden;">
                    <!-- Video Player -->
                    <video id="videoPlayer" controls style="width: 100%; height: auto;">
                        <!-- Video will be loaded dynamically here -->
                    </video>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {

            $('.button-menu-mobile').trigger('click');

            function formatFileSize(bytes, decimals = 2) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const dm = decimals < 0 ? 0 : decimals;
                const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
            }

            const mimeToExtension = {
                'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'pptx',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'docx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'xlsx',
                'application/msword': 'doc',
                'application/pdf': 'pdf',
                'application/zip': 'zip',
                'text/plain': 'txt',
                'image/jpeg': 'jpg',
                'image/png': 'png',
                'image/gif': 'gif',
                'application/vnd.ms-powerpoint': 'ppt',
                // Add more as needed
            };



            // Load FancyTree script first
            //var folderDetailsHtml = "<p>No folder details available.</p>";

            var app_path = $('#app_path').val();
            var file_id;

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
                                        <h5>Folder: ${response.folder_name}</h5>
                                        <button class="btn btn-sm btn-warning edit-memo-btn" data-folder-id="${node.key}">Edit Memo</button>
                                    </div>
                                    <p><strong>Date:</strong> ${response.folder_created_on ?? "N/A"}</p>
                                    <p><strong>Creator:</strong> ${response.folder_created_by ?? "Unknown"}</p>
                                    <p><strong>Description:</strong> ${response.folder_description ?? "No description available"}</p>
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
                                                <td>${file.file_size ? formatFileSize(file.file_size) : "N/A"}</td>
                                                <td>${mimeToExtension[file.file_type] ?? file.file_type ?? "N/A"}</td>
                                                <td>${file.uploaded_on ?? "N/A"}</td>
                                                <td>
                                                    <button class="btn btn-primary btn-xs view-file-btn demos" data-file="demo.pdf" data-file-path="${app_path}/${file.file_path}" data-file-id="${file.file_id}">
                                                        View File
                                                    </button>
                                                    <button class="btn btn-primary btn-xs view-file-btn download" data-file="demo.pptx" data-file-path="${app_path}/${file.file_path}">
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
                                                    <td>${response.folder_status ?? "Draft"}</td>
                                                </tr>
                                                <tr>
                                                    <th>Category</th>
                                                    <td>${response.folder_category ?? "PDM Access Request"}</td>
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
                console.log(file_path);
                var app_path = $('#app_path').val();
                $.getScript(app_path + '/include/PPTXjs/js/pptxjs.js', function () {
                    $.getScript(app_path + '/include/PPTXjs/js/divs2slides.js', function () {
                        renderPptx(file_path);
                    });
                });

                function renderPptx(file_path) {
                    const container = $("#resolte-contaniner");
                    container.html("<p id='loading-msg'>Loading presentation...</p>");

                    // Setup a timeout as a fallback
                    setTimeout(function () {
                        if (container.find(".slide").length === 0) {

                            console.warn("PPTX rendering failed. Attempting to convert to PDF...");

                            $.ajax({
                                url: app_path + '/conversion/toPDF',
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('#token').val()
                                },
                                data: {
                                    params: {
                                        file_id: file_id,
                                        file_path: file_path,
                                    }
                                },
                                success: function (response) {
                                    if (response.status === 'success') {
                                        var pdfPath = response.file_path;
                                        container.html(`<iframe src="${pdfPath}" width="100%" height="600px" style="border:none;"></iframe>`);
                                    } else {
                                        container.html("<p>Unable to view the file.</p>");
                                    }
                                },
                                error: function () {
                                    container.html("<p>Conversion failed.</p>");
                                }
                            });

                        }
                    }, 1000);

                    // Load presentation
                    container.pptxToHtml({
                        pptxFileUrl: file_path,
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
                file_id = $(this).data("file-id");
                var file_path = $(this).data("file-path");
                var file_name = file_path.split('/').pop();
                var extension = file_name.split('.').pop().toLowerCase();


                // Define supported video extensions
                var videoExtensions = ['mp4', 'webm', 'ogg', 'avi', 'mov', 'wmv', 'flv', 'mkv'];

                // Check if the file extension is a video
                if (videoExtensions.includes(extension)) {
                    // It's a video, so show the video modal
                    $('#viewVideoModal').modal('show');
                    var videoPlayer = $('#videoPlayer');

                    // Clear previous sources before adding new ones
                    videoPlayer.empty();

                    // Set video source based on the file path
                    videoPlayer.append('<source src="' + file_path + '" type="video/' + extension + '">');

                    // Check if the browser supports the format, and fallback if necessary
                    videoPlayer[0].load();  // Reload the video player to apply the source
                    videoPlayer[0].play();  // Attempt to play the video immediately

                } else {

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
                            url: file_path,
                            pdfSetting: {
                                setLang: "",
                                setLangFilesPath: ""
                            }
                        });
                    }
                }
            });

            // When modal is closed, reset video source (to stop video)
            $('#viewVideoModal').on('hidden.bs.modal', function () {
                var videoPlayer = $('#videoPlayer')[0];
                videoPlayer.pause();  // Pause the video when closing
                videoPlayer.src = '';  // Clear the source to stop the video
            });

            $(document).on("click", ".download", function () {
                var app_path = $('#app_path').val();
                var filePath = $(this).data('file-path');
                var link = document.createElement('a');
                link.href = filePath;
                link.download = '';  // Forces download in most browsers
                $('body').append(link);
                link.click();
            });

            $(document).on('click', '.edit-memo-btn', function () {
                var app_path = $('#app_path').val();
                const folderId = $(this).data('folder-id');
                if (folderId) {
                    window.location.href = `${app_path}/document/edit/${folderId}`;
                }
            });


        });
    </script>

@endsection
