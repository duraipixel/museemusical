@extends('platform.layouts.template')
@section('toolbar')
<style>
    .content {
  padding: 10px 0;
}
</style>
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        @include('platform.layouts.parts._breadcrum')
        @include('platform.layouts.parts._menu_add_button')
    </div>
</div>
@endsection
@section('content')
    <div id="kt_content_container" class="container-xxl">
        <div class="card">
            <div class="card-body py-4">
                <div class="row mb-2">
                    <div class="col-sm-12 text-start">
                        <div class="row">
                            <div class="col-8">
                                <form id="importform" method="POST" action="{{ route('products.bulk.upload')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label class="form-label">Select Import File</label>
                                                <input type="file" name="file" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-4 mt-3 pt-5">
                                            <button type="submit"  class="btn btn-primary mb-2">Import</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-4">
                                <label for=""> Sample Excel file </label>
                                <div class="mt-2">
                                    <a href="{{ asset('assets/data/productSample.xlsx') }}" > <i class="mdi mdi-file h2"></i> Download Sample</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('add_on_script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
    <script src="{{ asset('assets/js/datatable.min.js') }}"></script>

    <script>
        $(document).ready(function(){
            $("#importform").validate({
                submitHandler: function(form) {
                    var formData = new FormData(form);
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: formData,
                        contentType: false,
                        processData: false,
                       
                        success: function(response) {
                            
                            if( response.error == 0 ) {
                                toastr.success('Success', response.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 2000);
                            } else {
                                toastr.error('Error', response.message);
                            }
                            
                        }            
                    });		
                    
                }
            });
        })
    </script>
@endsection
