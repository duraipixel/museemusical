@csrf
<input type="hidden" name="id" value="{{ $data->id ?? '' }}">
<input type="hidden" name="tab-name" value="password">
<div class="col-md-8">
    <div class="row fv-row mb-7">
        <div class="col-md-3 text-md-end">
            <label class="fs-6 fw-bold form-label mt-3">
                <span class="required">Old Password</span>
            </label>
        </div>
        <div class="col-md-9">
            <input type="password" class="form-control form-control-solid" name="old_password" value="" />
        </div>
    </div>
    <div class="row fv-row mb-7">
        <div class="col-md-3 text-md-end">
            <label class="fs-6 fw-bold form-label mt-3">
                <span class="required"> Password</span>
            </label>
        </div>
        <div class="col-md-9">
            <input type="password" class="form-control form-control-solid" name="password" value="" />
        </div>
    </div>
    <div class="row fv-row mb-7">
        <div class="col-md-3 text-md-end">
            <label class="fs-6 fw-bold form-label mt-3">
                <span class="required">Change Password</span>
            </label>
        </div>
        <div class="col-md-9">
            
            <input type="password" class="form-control form-control-solid" name="password_confirmation" value="" />
        </div>
    </div>
</div>
               
                <div class="row">
                    <div class="col-md-9">
                       
                        <div class="card-footer py-5 text-center" id="kt_activities_footer">
                            <div class="text-end px-8">
                                <button type="reset" class="btn btn-light me-3" id="discard">Discard</button>
                                <button type="submit" class="btn btn-primary" data-kt-ecommerce-password-type="submit">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </div>
                @section('add_on_script')
<script>
                const cancelButton = element.querySelector('#discard');
            cancelButton.addEventListener('click', e => {
                alert()
                e.preventDefault();

                Swal.fire({
                    text: "Are you sure you would like to cancel?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, cancel it!",
                    cancelButtonText: "No, return",
                    customClass: {
                        confirmButton: "btn btn-primary",
                        cancelButton: "btn btn-active-light"
                    }
                }).then(function(result) {
                    if (result.value) {
                        commonDrawer.hide(); // Hide modal				
                    }
                });
            });



</script>
@endsection
          