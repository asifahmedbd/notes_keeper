@extends('layouts.app')
@section('title', 'Create New Memo')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item active">Create New Document</li>
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
            <select class="form-select form-control rounded-lg shadow-sm" id="category_select" name="category">
                <option value="" disabled selected>Select a category</option>
                <option value="create_new">➕ Create New Category</option>
                @foreach($flattenedCategories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
                
            </select>
        </div>

        <!-- Hidden Form for Creating New Category -->
        <div id="new_category_form" class="border p-3 rounded shadow-sm" style="display: none;">
            <h5>Create New Category</h5>
            
            <!-- Category Name -->
            <div class="mb-2">
                <label for="new_category_name" class="form-label">Category Name</label>
                <input type="text" id="new_category_name" name="new_category_name" class="form-control" placeholder="Enter category name">
            </div>

            <!-- Parent Category Selection -->
            <div class="mb-2">
                <label for="parent_category" class="form-label">Parent Category</label>
                <select id="parent_category" name="parent_category" class="form-select">
                    <option value="">No Parent (Root Category)</option>
                    @foreach($flattenedCategories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Create Category Button -->
            <button type="button" class="btn btn-success btn-sm" id="create_category_btn">
                <i class="fas fa-plus"></i> Create Category
            </button>
        </div>



        <!-- Document Text (Text Editor) -->
        <div class="mb-3">
            <label for="document_text" class="form-label">Document Text</label>
            <textarea class="form-control" id="document_text" name="document_text" required></textarea>
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
            editor.ui.view.editable.element.style.height = "400px"; // Set height
        })
        .catch(error => {
            console.error('Error initializing CKEditor:', error);
        });
});

</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const categorySelect = document.getElementById("category_select");
        const newCategoryForm = document.getElementById("new_category_form");
        const createCategoryBtn = document.getElementById("create_category_btn");

        // Show/hide new category form based on selection
        categorySelect.addEventListener("change", function () {
            if (this.value === "create_new") {
                newCategoryForm.style.display = "block";
            } else {
                newCategoryForm.style.display = "none";
            }
        });

        // Handle new category creation (AJAX)
        createCategoryBtn.addEventListener("click", function () {
            const newCategoryName = document.getElementById("new_category_name").value;
            const parentCategory = document.getElementById("parent_category").value;

            if (!newCategoryName.trim()) {
                alert("Please enter a category name.");
                return;
            }

            // Send AJAX request to create category
            fetch("{{ route('documents.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({
                    category_name: newCategoryName,
                    parent_id: parentCategory || null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Category created successfully!");
                    
                    // Add the new category to the dropdown dynamically
                    const newOption = document.createElement("option");
                    newOption.value = data.category_id;
                    newOption.textContent = data.category_name;
                    categorySelect.insertBefore(newOption, categorySelect.lastElementChild);

                    // Reset form and hide
                    document.getElementById("new_category_name").value = "";
                    newCategoryForm.style.display = "none";
                    categorySelect.value = data.category_id;
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Something went wrong.");
            });
        });
    });
</script>


@endsection