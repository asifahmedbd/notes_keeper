@extends('layouts.app')
@section('title', 'Create New Memo')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item active">Create New Memo</li>
@endsection

@section('content')

<style>
    #category {
        border: 1px solid #ddd;
        padding: 0.5rem;
        background-color: #f9f9f9;
        transition: 0.2s ease-in-out;
    }

    #category:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 5px rgba(74, 144, 226, 0.5);
        background-color: #fff;
    }

    #file-upload-table th, #file-upload-table td {
        vertical-align: middle;
        text-align: center;
    }

    #add-file-row {
        display: block;
        margin-top: 10px;
    }

    input[type="file"] {
        padding: 3px;
    }

</style>

<div class="container">
    <!-- <h1>Create New Document</h1> -->
    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Document Subject -->
        <div class="mb-3">
            <label for="subject" class="form-label">Document Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter document subject" required>
        </div>

        <!-- Category Dropdown -->
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select form-control rounded-lg shadow-sm" id="category" name="category" required>
                <option value="" disabled selected>Select a category</option>
                @foreach($flattenedCategories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>
        </div>


        <!-- Document Text (Text Editor) -->
        <div class="mb-3">
            <label for="document_text" class="form-label">Document Text</label>
            <textarea class="form-control" id="document_text" name="document_text" rows="10" required></textarea>
        </div>

        <!-- Multiple File Uploader -->
        <div class="mb-3">
            <label class="form-label">Upload Files</label>
            <table class="table table-bordered align-middle" id="file-upload-table">
                <thead class="table-light">
                    <tr>
                        <th>File</th>
                        <th>File Display Name</th>
                        <th>Comments</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="file" name="files[]" class="form-control" required></td>
                        <td><input type="text" name="display_name[]" class="form-control" placeholder="Display Name" required></td>
                        <td><input type="text" name="file_comments[]" class="form-control" placeholder="Comments"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row" disabled>&times;</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-success btn-sm" id="add-file-row">
                <i class="fas fa-plus"></i> Add More
            </button>
        </div>

        <!-- Document Information Section -->
        <div class="mb-4">
            <h4>Document Information</h4>

            <div class="row g-3 align-items-center" style="margin-top: 1rem;">
                <!-- Document Status -->
                <div class="col-md-4">
                    <label for="document_status" class="form-label">Document Status</label>
                    <select class="form-select form-control" id="document_status" name="document_status" required>
                        <option value="Draft">Draft</option>
                        <option value="Final">Final</option>
                        <option value="Archived">Archived</option>
                    </select>
                </div>

                <!-- Unit Dropdown -->
                <div class="col-md-4">
                    <label for="unit" class="form-label">Unit</label>
                    <input type="text" class="form-control" id="unit" name="unit" placeholder="Enter unit" required>
                </div>

                <!-- Keywords (Multiselect Text Input) -->
                <div class="col-md-4">
                    <label for="keywords" class="form-label">Keywords</label>
                    <input type="text" id="keywords" name="keywords" class="form-control" placeholder="Enter keywords, separated by commas">
                </div>
            </div>
        </div>

        <!-- Document Editors and Readers Section -->
        <div class="mb-4">
            <h4>Document Editors and Readers</h4>

            <div class="row g-3 align-items-center">
                <!-- Editors Dropdown -->
                <div class="col-md-6">
                    <label for="editors" class="form-label">Select Editors</label>
                    <select class="form-select form-control" id="editors" name="editors[]" multiple required>
                        <option value="Admin">Admin</option>
                        <option value="Manager">Manager</option>
                        <option value="Team Lead">Team Lead</option>
                        <option value="HR">HR</option>
                    </select>
                </div>

                <!-- Readers Dropdown -->
                <div class="col-md-6">
                    <label for="readers" class="form-label">Select Readers</label>
                    <select class="form-select form-control" id="readers" name="readers[]" multiple required>
                        <option value="Employee">Employee</option>
                        <option value="Manager">Manager</option>
                        <option value="Guest">Guest</option>
                        <option value="HR">HR</option>
                    </select>
                </div>
            </div>
        </div>




        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Document</button>
    </form>
</div>


<!-- Include a text editor library -->
<script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@36.0.1/build/ckeditor.js"></script>


 <script>
    document.addEventListener("DOMContentLoaded", () => {
        ClassicEditor
            .create(document.querySelector('#document_text'))
            .then(editor => {
                console.log('Editor initialized:', editor);
            })
            .catch(error => {
                console.error('Error initializing CKEditor:', error);
            });
    });

    document.addEventListener("DOMContentLoaded", () => {
        const fileTable = document.querySelector("#file-upload-table tbody");
        const addFileRowBtn = document.querySelector("#add-file-row");

        // Add new row
        addFileRowBtn.addEventListener("click", () => {
            const newRow = `
                <tr>
                    <td><input type="file" name="files[]" class="form-control" required></td>
                    <td><input type="text" name="display_name[]" class="form-control" placeholder="Display Name" required></td>
                    <td><input type="text" name="file_comments[]" class="form-control" placeholder="Comments"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">&times;</button>
                    </td>
                </tr>
            `;
            fileTable.insertAdjacentHTML('beforeend', newRow);
            updateRemoveButtons();
        });

        // Remove row
        fileTable.addEventListener("click", (e) => {
            if (e.target.classList.contains("remove-row")) {
                e.target.closest("tr").remove();
                updateRemoveButtons();
            }
        });

        // Enable/disable remove button
        const updateRemoveButtons = () => {
            const rows = fileTable.querySelectorAll("tr");
            rows.forEach((row, index) => {
                const removeBtn = row.querySelector(".remove-row");
                removeBtn.disabled = rows.length === 1; // Disable if only 1 row left
            });
        };

        updateRemoveButtons();
    });

</script>


@endsection