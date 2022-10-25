@extends('platform.layouts.template')
@section('toolbar')
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        @include('platform.layouts.parts._breadcrum')
    </div>
</div>
@endsection
@section('content')
<div id="kt_content_container" class="container-xxl">
 
    <div class="card mb-2 mb-xl-5">
        <div class="card-body pt-1 pb-0">
         
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder">
             
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5 active" href="#">Project</a>
                </li>
              
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#">Security</a>
                </li>
            
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#">API Keys</a>
                </li>
              
                <li class="nav-item mt-2">
                    <a class="nav-link text-active-primary ms-0 me-10 py-5" href="#">Logs</a>
                </li>
             
            </ul>
         
        </div>
    </div>


    <div class="card mb-5 mb-xl-10">
        <div id="kt_account_settings_profile_details" class="collapse show">
            @include('platform.global._general_form')
        </div>
    </div>
   
</div>
@endsection
@section('add_on_script')
    <script>
        const global_form_submit_url = "{{ route('global.save') }}";
    </script>
    
    <script src="{{ asset('assets/js/custom/account/settings/global-details.js') }}"></script>
@endsection