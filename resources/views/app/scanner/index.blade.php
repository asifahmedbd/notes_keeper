@extends('layouts.app')
@section('title', 'Directory Scanner')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="/">Home</a></li>
<li class="breadcrumb-item active">Directory Scanner</li>
@endsection

@section('content')

    <script src="/js/scanner.js"></script>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-body">
                <h5 class="card-title">Directory Scanner</h5>
                <p class="card-text">This function recursively scans the specified directory, identifying all files and subdirectories within it. It provides a comprehensive overview of the directory structure beneath the provided path. Each entity within the directory (be it a file or a subfolder) is captured and details about it, potentially including attributes like name, size, and modification date, are returned in a structured list.</p>
                <a href="javascript:void(0);" onclick="scanDirectory();" class="btn btn-danger waves-effect waves-light">Scan Directory Now!</a>
            </div>
        </div>
    </div>


@endsection
