@csrf
<input type="hidden" name="id" value="{{ $data->id ?? '' }}">
<input type="hidden" name="tab-name" value="myaccount">

<div class="row">
    <div class="col-md-6">
        <div class="row fv-row mb-7 ">
            <div class="col-md-3 text-md-end">
                <label class="required fs-6 fw-bold form-label mt-3">
                    Name
                </label>
            </div>
            <div class="col-md-9">
                <input type="text" class="form-control form-control-solid mb-3 mb-lg-0" name="name" value="{{ $data->name ?? '' }}" />
            </div>
        </div>
        <div class="row fv-row mb-7">
            <div class="col-md-3 text-md-end">
                <label class="fs-6 fw-bold form-label mt-3">
                    <span class="required">Email</span>
                    
                </label>
            </div>
            <div class="col-md-9">
                <input type="text" class="form-control form-control-solid"   name="email" value="{{ $data->email ?? '' }}" readonly />
            </div>
        </div>
        <div class="row fv-row mb-7">
            <div class="col-md-3 text-md-end">
                <label class="fs-6 fw-bold form-label mt-3">
                    <span class="required">Mobile</span>
                    
                </label>
            </div>
            <div class="col-md-9">
                <input type="text" class="form-control form-control-solid mobile_number" maxlength="10"  name="mobile_number" value="{{ $data->mobile_no ?? '' }}" />
            </div>
        </div>
        
        <div class="row fv-row mb-7">
            <div class="col-md-3 text-md-end">
                <label class="fs-6 fw-bold form-label mt-3">
                    <span>Address</span>
                    
                </label>
            </div>
            <div class="col-md-9">
                <textarea class="form-control form-control-solid" name="address">{{ $data->address ?? '' }}</textarea>
            </div>
        </div>
    </div>
    <div class="col-md-1">

    </div>
    <div class="col-md-5">
        <div class="col-md-4">

            <div class="fv-row mb-7">
                <label class="d-block fw-bold fs-6 mb-5">Profile Image</label>

                <div class="form-text">Allowed file types: png, jpg,
                    jpeg.</div>
            </div>
            <input id="image_remove_image" type="hidden" name="image_remove_image" value="no">
            <div class="image-input image-input-outline manual-image" data-kt-image-input="true"
                style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                @if ($data->image ?? '')
                    <div class="image-input-wrapper w-125px h-125px manual-image"
                        id="manual-image"
                        style="background-image: url({{ asset('/') . $data->image }});">
                    </div>
                @else
                    <div class="image-input-wrapper w-125px h-125px manual-image"
                        id="manual-image"
                        style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                    </div>
                @endif
                <label
                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="change" data-bs-toggle="tooltip"
                    title="Change avatar">
                    <i class="bi bi-pencil-fill fs-7"></i>
                    <input type="file" name="avatar" id="readUrl"
                        accept=".png, .jpg, .jpeg" />
                </label>

                <span
                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                    title="Cancel avatar">
                    <i class="bi bi-x fs-2"></i>
                </span>
                <span
                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                    data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                    title="Remove avatar1">
                    <i class="bi bi-x fs-2" id="avatar_remove_logo"></i>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-9 offset-md-3">
        <div class="separator mb-6"></div>
        
        <div class="card-footer py-5 text-center" id="kt_activities_footer">
            <div class="text-end px-8">
                <button type="reset" class="btn btn-light me-3" id="discard">Discard</button>
                <button type="submit" class="btn btn-primary" data-kt-ecommerce-settings-type="submit">
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
const submitButton = element.querySelector('[data-kt-ecommerce-settings-type="submit"]');
 document.getElementById('readUrl').addEventListener('change', function() {
    // console.log("111");
    if (this.files[0]) {
        var picture = new FileReader();
        picture.readAsDataURL(this.files[0]);
        picture.addEventListener('load', function(event) {
            console.log(event.target);
            let img_url = event.target.result;
            $('#manual-image').css({
                'background-image': 'url(' + event.target.result + ')'
            });
        });
    }
});
document.getElementById('avatar_remove_logo').addEventListener('click', function() {
    $('#image_remove_image').val("yes");
    $('#manual-image').css({
        'background-image': ''
    });
});



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