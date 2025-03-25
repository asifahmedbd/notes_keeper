<div class="row">
    <div class="col-md-12">
        <div class="page-title-box">
            <div class="page-title-right">

                @if(Route::currentRouteName() == 'view.company')
                    <div class="form-group">
                        <div class="input-group input-group-sm">

                            <select class="form-control" id="view_company_id">
                                @foreach($companies as $companyObject)
                                <option value="{{ $companyObject->company_id }}" {{ $companyObject->company_id == $company_data->company_id ? 'selected':'' }}>{{ $companyObject->company_name }} - {{ $companyObject->company_code }}</option>
                                @endforeach
                            </select>

                            <div class="input-group-append">
                                <span class="input-group-text bg-blue border-blue text-white">
                                    <i class="mdi mdi-factory font-13"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <ol class="breadcrumb m-0">
                        @yield('breadcrumb')
                    </ol>
                @endif

            </div>
            <h4 class="page-title">@yield('title')</h4>
        </div>
    </div>
</div>
