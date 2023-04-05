@extends('platform.layouts.template')
@section('toolbar')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            @include('platform.layouts.parts._breadcrum')
        </div>
    </div>
    <style>
        label.error {
            color: red;
        }

        .bulk_btn_pane {
            width: min-content;
            position: relative;
        }

        .bulk_input {
            position: absolute;
            width: 35px;
            border: 1px solid #ddd;
            padding: 1px 4px;
        }

        .bulk_btn {
            position: absolute;
            float: right;
            left: 35px;
            width: 83px;
            background: #489fd1;
            border: 1px solid #489fd1;
            color: white;
        }

        .bulk_input:hover {
            border: 1px solid #eee;
        }

        .bulk_btn:hover {
            background: white;
            color: #489fd1;
            font-weight: 8000;
            border: 1px solid #489fd1;
        }
    </style>
@endsection
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <form id="kt_ecommerce_add_product_form" method="POST" class="form d-flex flex-column flex-lg-row">
                    @csrf
                    <input type="hidden" name="id" value="{{ $info->id ?? '' }}">
                    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                        @include('platform.product.form.parts._common_side')
                    </div>

                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                        <!--begin:::Tabs-->
                        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-n2">
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4 active" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_general">General</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_description">Descriptions</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_filter">Filter</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_meta">Meta Tags</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_related">Related Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-active-primary product-tab pb-4" data-bs-toggle="tab"
                                    href="#kt_ecommerce_add_product_linkes">Links</a>
                            </li>
                        </ul>

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

                            <div class="tab-pane fade" id="kt_ecommerce_add_product_linkes" role="tab-panel">
                                @include('platform.product.form.links.index')
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="javascript:void(0);" id="kt_ecommerce_add_product_cancel"
                                class="btn btn-light me-5">Cancel</a>

                            <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                                <span class="indicator-label">Save Changes</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('add_on_script')
    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script>
        function showCategoryTax(category_id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('get.product.taxCategory') }}",
                type: "POST",
                data: {
                    category_id: category_id
                },
                success: function(res) {
                    $('#mrp_tax').html(`(Inclusive Tax ( ${res}%)) `);
                }
            })
        }

        @if (isset($info->id) && !empty($info->id))
            addVariationRow('{{ $info->id }}');
        @endif
        $('.product-tab').click(function() {

            let types = $(this).attr('href');
            var checkArray = ['#kt_ecommerce_add_product_meta', '#kt_ecommerce_add_product_filter',
                '#kt_ecommerce_add_product_related'
            ];
            if (checkArray.includes(types)) {
                console.log('welcome');
            } else {
                return true;
            }

        });

        var isImage = false;
        var product_url = "{{ route('products') }}";
        var product_add_url = "{{ route('products.save') }}";
        var remove_image_url = "{{ route('products.remove.image') }}";
        var remove_brochure_url = "{{ route('products.remove.brochure') }}";
        var brochure_upload_url = "{{ route('products.upload.brochure') }}";
        var gallery_upload_url = "{{ route('products.upload.gallery') }}";

        var myDropzone = new Dropzone("#kt_ecommerce_add_product_media", {
            autoProcessQueue: false,
            url: gallery_upload_url, // Set the url for your upload script location
            headers: {
                'x-csrf-token': document.head.querySelector('meta[name="csrf-token"]').content,
            },
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 10,
            maxFilesize: 10, // MB
            parallelUploads: 10,
            uploadMultiple: true,
            addRemoveLinks: true,
            acceptedFiles: "image/*",
            accept: function(file, done) {

                if (file.name == "wow.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }

            },
            init: function() {
                let dropZone = this;
                let jsonData = '{!! $images !!}';
                // jsonData = JSON.stringify(jsonData);
                jsonData = JSON.parse(jsonData);

                if (jsonData.length > 0) {
                    for (let index = 0; index < jsonData.length; index++) {
                        let formIns = jsonData[index];
                        // If the thumbnail is already in the right size on your server:
                        let mockFile1 = {
                            name: formIns.name,
                            size: formIns.size,
                            id: formIns.id,
                            order_by: formIns.order_by
                        };
                        let callback = null; // Optional callback when it's done
                        let crossOrigin = null; // Added to the `img` tag for crossOrigin handling
                        let resizeThumbnail = false; // Tells Dropzone whether it should resize the image first
                        dropZone.displayExistingFile(mockFile1, formIns.url, callback, crossOrigin,
                            resizeThumbnail);

                    }
                }

                // this.on("addedfile", function(file) {
                //     // Create the remove button
                //     var removeButton = Dropzone.createElement("<button>Remove file</button>");
                //     // Capture the Dropzone instance as closure.
                //     var _this = this;
                //     // Listen to the click event
                //     removeButton.addEventListener("click", function(e) {
                //         // Make sure the button click doesn't submit the form:
                //         e.preventDefault();
                //         e.stopPropagation();
                //         // Remove the file preview.
                //         _this.removeFile(file);
                //         // If you want to the delete the file on the server as well,
                //         // you can do the AJAX request here.
                //     });
                //     // Add the button to the file preview element.
                //     file.previewElement.appendChild(removeButton);
                // });

            },
            stop: function() {
                var queue = dropZone.getAcceptedFiles();
                newQueue = [];
                $('#kt_ecommerce_add_product_media .dz-preview .dz-filename [data-dz-name]').each(function(
                    count, el) {
                    var name = el.innerHTML;
                    queue.forEach(function(file) {
                        if (file.name === name) {
                            newQueue.push(file);
                        }
                    });
                });
                dropZone.files = newQueue;
            },
            removedfile: function(file) {
                console.log(file);
                console.log('started');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        removeGalleryImage(file.id);
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                        file.previewElement.remove();
                    }
                })



            }

        });

        var myBrocheureDropzone = new Dropzone("#kt_ecommerce_add_product_brochure", {
            autoProcessQueue: false,
            url: brochure_upload_url, // Set the url for your upload script location
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 1,
            maxFilesize: 10, // MB
            addRemoveLinks: true,

            accept: function(file, done) {
                if (file.name == "wow.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            },
            sending: function(file, xhr, formData) {
                formData.append("_token", $("meta[name='csrf-token']").attr("content"));
            },
            success: function(file, serverFileName) {
                // let fileList[file.name] = {"fid" : serverFileName };
                console.log(serverFileName);
                console.log(file);

            },
            init: function() {
                let dropZone = this;
                let jsonData = '{!! $brochures !!}';
                jsonData = JSON.parse(jsonData);
                // console.log(jsonData);
                if (Object.keys(jsonData).length > 0) {
                    let formIns = jsonData;
                    // If the thumbnail is already in the right size on your server:
                    let mockFile1 = {
                        name: formIns.name,
                        size: formIns.size,
                        id: formIns.id
                    };
                    let callback = null; // Optional callback when it's done
                    let crossOrigin = null; // Added to the `img` tag for crossOrigin handling
                    let resizeThumbnail = false; // Tells Dropzone whether it should resize the image first
                    dropZone.displayExistingFile(mockFile1, formIns.url, callback, crossOrigin,
                        resizeThumbnail);

                    var a = document.createElement('a');
                    a.setAttribute('href', formIns.url);
                    a.setAttribute('rel', "nofollow");
                    a.setAttribute('target', "_blank");
                    a.setAttribute('download', formIns.name);

                    a.innerHTML = "<br>download";
                    $('#kt_ecommerce_add_product_brochure').find(".dz-remove").after(a);
                }

            },
            removedfile: function(file) {
                Swal.fire({
                    text: "Are you sure you would like to remove?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, remove it!",
                    cancelButtonText: "No, return",
                    customClass: {
                        confirmButton: "btn btn-primary",
                        cancelButton: "btn btn-active-light"
                    }
                }).then(function(result) {
                    if (result.value) {
                        removeBrochure(file.id)
                        file.previewElement.remove();
                    }
                });

            }
        });


        function removeGalleryImage(productImageId) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: remove_image_url,
                type: 'POST',
                data: {
                    id: productImageId
                },
                success: function(res) {
                    console.log(res);
                }
            });

        }

        function removeBrochure(product_id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: remove_brochure_url,
                type: 'POST',
                data: {
                    id: product_id
                },
                success: function(res) {

                }
            });

        }

        function addVariationRow(id = '') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('products.attribute.row') }}",
                type: "POST",
                data: {
                    product_id: id
                },
                success: function(res) {
                    $('#formRepeaterId').append(res);
                }

            });
        }

        $("body").on("click", ".removeRow", function() {
            $(this).parents(".childRow").remove();
        })

        var productCancelButton;
        productCancelButton = document.querySelector('#kt_ecommerce_add_product_cancel');
        productCancelButton.addEventListener('click', function(e) {
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
                    window.location.href = product_url
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Your form has not been cancelled!.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        }
                    });
                }
            });
        });



        // Define variables

        // Get elements
        $(document).ready(function() {
            $('#kt_ecommerce_add_product_form').validate({
                rules: {
                    product_name: "required",
                    sku: "required",
                    category_id: "required",
                    brand_id: "required",
                    base_price: "required",
                },
                messages: {
                    product_name: "Product Name is required",
                    sku: "Product Sku is required",
                    category_id: "Category is required",
                    brand_id: "Brand is required",
                    base_price: "Base is required",
                },
                submitHandler: function(form) {
                    var action = "{{ route('products.save') }}";
                    var forms = $('#kt_ecommerce_add_product_form')[0];
                    var formData = new FormData(forms);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('products.save') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        cache: false,
                        processData: false,
                        beforeSend: function() {
                            submitButton.setAttribute('data-kt-indicator', 'on');
                            submitButton.disabled = true;
                        },
                        success: function(res) {
                            if (res.error == 1) {
                                // Remove loading indication
                                submitButton.removeAttribute('data-kt-indicator');
                                // Enable button
                                submitButton.disabled = false;
                                let error_msg = res.message
                                Swal.fire({
                                    html: res.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            } else {

                                if (res.product_id) {

                                    myDropzone.processQueue();
                                    myDropzone.on("addedfiles", (file) => {
                                        //    console.log( myDropzone.hiddenFileInput );
                                    });

                                    myBrocheureDropzone.processQueue();

                                }

                                submitButton.removeAttribute('data-kt-indicator');
                                // Enable button
                                submitButton.disabled = false;
                                setTimeout(() => {
                                    if (res.isUpdate) {
                                        location.reload();
                                    } else {
                                        window.location.href = product_url;
                                    }
                                }, 500);
                                Swal.fire({
                                    // text: "Thank you! You've updated Products",
                                    text: res.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function(result) {
                                    if (result.isConfirmed) {

                                        // window.location.href=product_url;

                                    }
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).ready(function() {
            $('#related_product').select2();
            $('#cross_selling_product').select2();
            $("body").on("click", ".removeUrlRow", function() {
                $(this).parents(".childUrlRow").remove();
            })

            $('.select2-search__field').on('keydown', function(e) {
                e.preventDefault();
                if (e.keyCode) {
                    return false;
                }

            });
        });


        function addLinks() {
            var addRow = $('#child-url').clone();
            $("#child-url").clone().appendTo("#formRepeaterUrl").find("input[type='text']").val("");
        }

        function changeOrder(image_id) {
            console.log(image_id, 'iamge_id');
            var img_value = $('#image_order_' + image_id).val();
            $.ajax({
                url: "{{ route('products.image.order') }}",
                type: 'POST',
                data: {
                    image_id: image_id,
                    order_by: img_value
                },
                success: function(res) {
                    toastr.success('Image order has been set successfully');
                }
            })
        }
    </script>
    <script src="{{ asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/custom/apps/ecommerce/catalog/save-product.js') }}"></script>
@endsection
