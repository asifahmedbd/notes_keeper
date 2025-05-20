@extends('layouts.app')
@section('title', 'Edit Memo')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active">Edit Document</li>
@endsection

@section('content')

    <script src="{{ env('APP_PATH') }}/js/category.js"></script>
    <script>
        var csrfToken = "{{ csrf_token() }}";
    </script>

    <div class="row">
        <div class="col-md-12">
            <div class="card-box p-2">
                <form id="memo-create" action="{{ route('update.document') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">

                        <div class="col-md-6">
                            <label for="subject" class="form-label">Document Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter document subject"  value="{{ old('document_subject', $memo->document_subject) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="current_category_name">Selected Category</label>
                            <input type="text" readonly="" class="form-control-plaintext" id="current_category_name" value="{{ $memo->category->category_name ?? 'Root' }}">
                        </div>

                        <div class="col-md-3">

                            <button type="button" class="btn btn-sm btn-dark waves-effect waves-light float-right" onclick="openCategorySelector();">
                                <i class="mdi mdi-plus-circle mr-1"></i>Select Category
                            </button>
                        </div>

                        <div class="col-md-12 mt-2">
                            <label for="document_text" class="form-label">Document Text</label>
                            <textarea class="form-control" id="document_text" name="document_text">{{ old('document_text', $memo->document_text) }}</textarea>
                        </div>

                    </div>

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
                                <input type="text" class="form-control" id="unit" name="unit" value="{{ old('doc_unit', $memo->doc_unit) }}" placeholder="Enter unit" required>
                            </div>

                            <!-- Keywords (Multiselect Text Input) -->
                            <div class="col-md-4">
                                <label for="keywords" class="form-label">Keywords</label>
                                <input type="text" id="keywords" name="keywords" class="form-control" placeholder="Enter keywords, separated by commas" value="{{ old('doc_keywords', $memo->doc_keywords) }}">
                            </div>
                        </div>
                    </div>

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

                    <button type="submit" class="btn btn-primary">Update Document</button>
                    <input type="hidden" name="document_id" value="{{ $memo->document_id }}">
                    <input type="hidden" name="category_id" id="category_id" value="{{ $memo->category_id ?? 0 }}">
                </form>
            </div>
        </div>
    </div>

    <div id="select_category_modal" class="modal bounceInDown animated">
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">Category Selector</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body p-2">

                <input type="hidden" id="current_category_id" name="current_category_id" value="{{  $memo->category_id ?? 0 }}">

                    <div class="row">

                        <div class="col-md-6" id="tree">
                            <div id="fancytree_category_selector" class="border p-2" style="max-height: 400px; overflow-y: auto;"></div>
                        </div>

                        <div class="col-md-6">

                            <h4 class="mb-3 header-title">Current Selected Category: <span class="current_category_name">Root</span></h4>

                            <div class="form-group">
                                <label for="category_name">Category Name</label>
                                <input type="text" class="form-control" id="category_name" placeholder="Enter Category Name">
                                <small id="category_name_help" class="form-text text-muted">This Category Will Be Created Under <span class="current_category_name">Root</span></small>
                            </div>

                            <button type="button" id="create_category_button" class="btn btn-primary waves-effect waves-light">Create</button>

                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-blue waves-effect waves-light" data-dismiss="modal" id="select_category_button">Save</button>
                </div>
            </div>
        </div>
    </div>

@endsection