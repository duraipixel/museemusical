@extends('platform.layouts.template')
@section('toolbar')
<div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        @include('platform.layouts.parts._breadcrum')
    </div>
</div>
@endsection
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <form id="kt_ecommerce_add_product_form" method="POST" class="form d-flex flex-column flex-lg-row" >
                @csrf
                <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                    @include('platform.product.form.parts._common_side')
                </div>
             
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <!--begin:::Tabs-->
                    <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-n2">
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_ecommerce_add_product_general">General</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_ecommerce_add_product_description">Descriptions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_ecommerce_add_product_filter">Filter</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_ecommerce_add_product_meta">Meta Tags</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#kt_ecommerce_add_product_related">Related Products</a>
                        </li>
                    </ul>
                    <!--end:::Tabs-->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general" role="tab-panel">
                            @include('platform.product.form.general.general')
                        </div>
                      
                        <div class="tab-pane fade" id="kt_ecommerce_add_product_description" role="tab-panel">
                            @include('platform.product.form.description.description')
                        </div>

                        <div class="tab-pane fade" id="kt_ecommerce_add_product_filter" role="tab-panel">
                            @include('platform.product.form.filter.filter')
                        </div>

                        <div class="tab-pane fade" id="kt_ecommerce_add_product_meta" role="tab-panel">
                            @include('platform.product.form.meta.meta')
                        </div>

                        <div class="tab-pane fade" id="kt_ecommerce_add_product_related" role="tab-panel">
                            @include('platform.product.form.related.related')
                        </div>
                    </div>
                    <!--end::Tab content-->
                    <div class="d-flex justify-content-end">
                        <!--begin::Button-->
                        <a href="javascript:void(0);" id="kt_ecommerce_add_product_cancel"  class="btn btn-light me-5">Cancel</a>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                            <span class="indicator-label">Save Changes</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <!--end::Button-->
                    </div>
                </div>
                <!--end::Main column-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
    
@endsection
@section('add_on_script')
<script src="{{ asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
{{-- <script src="{{ asset('assets/js/custom/apps/ecommerce/catalog/save-product.js') }}"></script> --}}
{{-- <script src="{{ asset('assets/js/custom/apps/calendar/calendar.js') }}"></script> --}}

<script>
    var add_url = "{{ route('products.save') }}";
    var KTProductSave = function() {
        // Shared variables
        const form = document.querySelector('#kt_ecommerce_add_product_form');
        // Init add schedule modal
        var initAddRole = () => {
            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            var validator = FormValidation.formValidation(
                form, {
                    fields: {
                        'product_name': {
                            validators: {
                                notEmpty: {
                                    message: 'Product Name is required'
                                }
                            }
                        },
                        'category_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Category is required'
                                }
                            }
                        },
                        'brand_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Brand is required'
                                }
                            }
                        },
                        'label_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Product Label is required'
                                }
                            }
                        },
                        'tag_id': {
                            validators: {
                                notEmpty: {
                                    message: 'Product Tag is required'
                                }
                            }
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.fv-row',
                            eleInvalidClass: '',
                            eleValidClass: ''
                        }),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'fa fa-check',
                            invalid: 'fa fa-times',
                            validating: 'fa fa-refresh',
                        }),
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
            const submitButton = element.querySelector('#kt_ecommerce_add_product_submit');
            // submitButton.addEventListener('click', function(e) {
            submitButton.addEventListener('click', function (e) {
                // Prevent default button action
                e.preventDefault();
                // Validate form before submit
                if (validator) {
                    validator.validate().then(function(status) {
                        
                        if (status == 'Valid') {
                            var from = $('#from').val();
                            var form = $('#kt_ecommerce_add_product_form')[0]; 
                            var formData = new FormData(form);
                            submitButton.setAttribute('data-kt-indicator', 'on');
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
                                    if (res.error == 1) {
                                        // Remove loading indication
                                        submitButton.removeAttribute('data-kt-indicator');
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
                                        if( from != '' ) {
                                            getProductCategoryDropdown(res.categoryId);
                                            return false;
                                        }
                                        dtTable.ajax.reload();
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

        const initBrochureDropzone = () => {
            var myDropzone = new Dropzone("#kt_ecommerce_add_product_brochure", {
                url: "{{ route('products.upload.brochure') }}", // Set the url for your upload script location
                paramName: "file", // The name that will be used to transfer the file
                maxFiles: 10,
                maxFilesize: 10, // MB
                addRemoveLinks: true,
                accept: function (file, done) {
                    if (file.name == "wow.jpg") {
                        done("Naha, you don't.");
                    } else {
                        done();
                    }
                }
            });
        }

        return {
            init: function() {
                initAddRole();
                initBrochureDropzone();
            }
        };
    }();
    // On document ready
    KTUtil.onDOMContentLoaded(function() {
        KTProductSave.init();
    });




       
</script>
    
@endsection
