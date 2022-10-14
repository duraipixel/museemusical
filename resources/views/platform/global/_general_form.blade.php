<form id="kt_account_global_form" class="form" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="1">
    <div class="card-body border-top p-9">
      
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Favicon</label>

            <div class="col-lg-8">
                <input id="image_remove_favicon" type="hidden" name="image_remove_favicon" value="no">
                <div class="image-input image-input-outline manual-image-favicon" data-kt-image-input="true"
                    style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                    @if ($data->favicon ?? '')
                        <div class="image-input-wrapper w-125px h-125px manual-image-favicon"
                            id="manual-image-favicon"
                            style="background-image: url({{ asset('/') . $data->favicon }});">
                        </div>
                    @else
                        <div class="image-input-wrapper w-125px h-125px manual-image-favicon"
                            id="manual-image-favicon"
                            style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                        </div>
                    @endif
                    <label
                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                        title="Change avatar">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <input type="file" name="favicon" id="readUrlfavicon"
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
                        <i class="bi bi-x fs-2" id="avatar_remove_favicon"></i>
                    </span>
                </div>
            </div>
        </div> 
        <br>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Logo</label>

            <div class="col-lg-8">
                <input id="image_remove_logo" type="hidden" name="image_remove_logo" value="no">
                <div class="image-input image-input-outline manual-image-logo" data-kt-image-input="true"
                    style="background-image: url({{ asset('userImage/no_Image.jpg') }})">
                    @if ($data->logo ?? '')
                        <div class="image-input-wrapper w-125px h-125px manual-image-logo"
                            id="manual-image-logo"
                            style="background-image: url({{ asset('/') . $data->logo }});">
                        </div>
                    @else
                        <div class="image-input-wrapper w-125px h-125px manual-image-logo"
                            id="manual-image-logo"
                            style="background-image: url({{ asset('userImage/no_Image.jpg') }});">
                        </div>
                    @endif
                    <label
                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                        title="Change avatar">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <input type="file" name="logo" id="readUrllogo"
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
       
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label required fw-bold fs-6">Site Name</label>
            <div class="col-lg-8 fv-row">
                <input type="text" name="site_name" class="form-control form-control-lg form-control-solid" placeholder="Site name" value="{{ $data->site_name ?? '' }}" />
            </div>
        </div>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">
                <span class="required">Contact Phone</span>
                {{-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Phone number must be active"></i> --}}
            </label>
            <div class="col-lg-8 fv-row">
                <input type="tel" name="site_mobile_number" class="form-control form-control-lg form-control-solid" placeholder="Phone number" value="{{ $data->site_mobile_no ?? '' }}" />
            </div>
        </div>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">
                <span class="required">Contact Email</span>
                {{-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Phone number must be active"></i> --}}
            </label>
            <div class="col-lg-8 fv-row">
                <input type="email" name="site_email" class="form-control form-control-lg form-control-solid" placeholder="Site Email" value="{{ $data->site_email ?? '' }}" />
            </div>
        </div>
        
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Copyrights</label>
            <div class="col-lg-8 fv-row">
                <input type="text" name="copyrights" class="form-control form-control-lg form-control-solid" placeholder="Copyrights" value="{{ $data->copyrights ?? '' }}" />
            </div>
        </div>
    </div>
    <!--end::Card body-->
    <!--begin::Actions-->
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="reset" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" class="btn btn-primary" id="kt_account_global_submit">Save Changes</button>
    </div>
    <!--end::Actions-->
</form>
<script>
    
    document.getElementById('readUrlfavicon').addEventListener('change', function() {
        console.log("111");
        if (this.files[0]) {
            var picture = new FileReader();
            picture.readAsDataURL(this.files[0]);
            picture.addEventListener('load', function(event) {
                console.log(event.target);
                let img_url = event.target.result;
                $('#manual-image-favicon').css({
                    'background-image': 'url(' + event.target.result + ')'
                });
            });
        }
    });
    document.getElementById('avatar_remove_favicon').addEventListener('click', function() {
        alert()
        $('#image_remove_favicon').val("yes");
        $('#manual-image-favicon').css({
            'background-image': ''
        });
    });

    document.getElementById('readUrllogo').addEventListener('change', function() {
        console.log("111");
        if (this.files[0]) {
            var picture = new FileReader();
            picture.readAsDataURL(this.files[0]);
            picture.addEventListener('load', function(event) {
                console.log(event.target);
                let img_url = event.target.result;
                $('#manual-image-logo').css({
                    'background-image': 'url(' + event.target.result + ')'
                });
            });
        }
    });
    document.getElementById('avatar_remove_logo').addEventListener('click', function() {
        alert()
        $('#image_remove_logo').val("yes");
        $('#manual-image-logo').css({
            'background-image': ''
        });
    });
</script>
<script>
    var add_url = "{{ route('global.save') }}";
 

  var KTUsersAddRole = function() {
        // Shared variables
        const element = document.getElementById('kt_common_add_form');
        const form = element.querySelector('#kt_account_global_form');
        const modal = new bootstrap.Modal(element);

        const drawerEl = document.querySelector("#kt_common_add_form");
        const commonDrawer = KTDrawer.getInstance(drawerEl);


        // Init add schedule modal
        var initAddRole = () => {

            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'site_name': {
                            validators: {
                                notEmpty: {
                                    message: 'Site name is required'
                                }
                            }
                        },
                        'site_mobile_number': {
                            validators: {
                                notEmpty: {
                                    message: 'Site Mobile Number is required'
                                }
                            }
                        },
                        'site_email': {
                            validators: {
                                notEmpty: {
                                    message: 'Site Email is required'
                                }
                            }
                        },
                    },

                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.fv-row',
                            eleInvalidClass: '',
                            eleValidClass: ''
                        })
                    }
                }
            );

            // Cancel button handler
            const cancelButton = element.querySelector('#discard');
            cancelButton.addEventListener('click', e => {
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

            // Submit button handler
            const submitButton = element.querySelector('[data-kt-order_status-modal-action="submit"]');
            // submitButton.addEventListener('click', function(e) {
            $('#kt_account_global_form').submit(function(e) {
// alert()

                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        if (status == 'Valid') {

                            var formData = new FormData(document.getElementById(
                                "kt_account_global_form"));
                            submitButton.setAttribute('data-kt-indicator', 'on');
                            // Disable button to avoid multiple click 
                            submitButton.disabled = true;
                            //call ajax call
                            $.ajax({
                                url: add_url,
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                beforeSend: function() {},
                                success: function(res) {
                                        alert(res)

                                    if (res.error == 1) {
                                        // Remove loading indication
                                        submitButton.removeAttribute(
                                            'data-kt-indicator');
                                        // Enable button
                                        submitButton.disabled = false;
                                        let error_msg = res.message
                                        Swal.fire({
                                            text: res.message,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        });
                                    } else {
                                        // dtTable.ajax.reload();
                                        Swal.fire({
                                            text: res.message,
                                            icon: "success",
                                            buttonsStyling: false,
                                            confirmButtonText: "Ok, got it!",
                                            customClass: {
                                                confirmButton: "btn btn-primary"
                                            }
                                        }).then(function(result) {
                                            if (result
                                                .isConfirmed) {
                                                commonDrawer
                                                    .hide();

                                            }
                                        });
                                    }
                                }
                            });

                        } else {
                            // Show popup warning. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            Swal.fire({
                                text: "Sorry, looks like there are some errors detected, please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            });
                        }
                    });
                }
            });


        }

        // Select all handler
        const handleSelectAll = () => {
            // Define variables
            const selectAll = form.querySelector('#kt_order_stautsorder_status_select_all');
            const allCheckboxes = form.querySelectorAll('[type="checkbox"]');

            // Handle check state
            selectAll.addEventListener('change', e => {
                // Apply check state to all checkboxes
                allCheckboxes.forEach(c => {
                    c.checked = e.target.checked;
                });
            });

        }


        return {
            // Public functions
            init: function() {
                initAddRole();
                handleSelectAll();
            }
        };
    }();

    // On document ready

    KTUtil.onDOMContentLoaded(function() {
        KTUsersAddRole.init();
    });

    $('.common-checkbox').click(function() {
        $("#kt_order_stauts_select_all").prop("checked", false);
    });
</script>