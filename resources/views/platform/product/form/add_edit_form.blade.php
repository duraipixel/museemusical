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
                <input type="hidden" name="id" value="{{ $info->id ?? '' }}">
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
<script>
    var isImage = false;
    var add_url = "{{ route('products.save') }}";
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
            parallelUploads : 10,
            uploadMultiple: true,
            addRemoveLinks: true,
            acceptedFiles: "image/*", 
            accept: function (file, done) {
                
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
                if( jsonData.length > 0 ) {
                    for (let index = 0; index <  jsonData.length; index++) {
                        let formIns = jsonData[index];
                        // If the thumbnail is already in the right size on your server:
                        let mockFile1 = {name:formIns.name,size:formIns.size, id:formIns.id};
                        let callback = null; // Optional callback when it's done
                        let crossOrigin = null; // Added to the `img` tag for crossOrigin handling
                        let resizeThumbnail = false; // Tells Dropzone whether it should resize the image first
                        dropZone.displayExistingFile(mockFile1, formIns.url, callback, crossOrigin, resizeThumbnail);

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
            removedfile: function (file) {
                console.log( file );
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
            
            accept: function (file, done) {
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
                console.log( serverFileName );
                console.log( file );
               
            },
            init: function() {
                let dropZone = this;
                let jsonData = '{!! $brochures !!}';
                jsonData = JSON.parse(jsonData);
                // console.log(jsonData);
                if( Object.keys(jsonData).length > 0 ) {
                    let formIns = jsonData;
                    // If the thumbnail is already in the right size on your server:
                    let mockFile1 = {name:formIns.name,size:formIns.size, id:formIns.id};
                    let callback = null; // Optional callback when it's done
                    let crossOrigin = null; // Added to the `img` tag for crossOrigin handling
                    let resizeThumbnail = false; // Tells Dropzone whether it should resize the image first
                    dropZone.displayExistingFile(mockFile1, formIns.url, callback, crossOrigin, resizeThumbnail);

                    var a = document.createElement('a');
                    a.setAttribute('href',formIns.url);
                    a.setAttribute('rel',"nofollow");
                    a.setAttribute('target',"_blank");
                    a.setAttribute('download',formIns.name);
                                        
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

        
    function removeGalleryImage( productImageId ) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }); 
        $.ajax({
            url: remove_image_url,
            type: 'POST',
            data: {id:productImageId},
            success:function(res) {
                console.log( res );
            }
        });

    }

    function removeBrochure( product_id ) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }); 
        $.ajax({
            url: remove_brochure_url,
            type: 'POST',
            data: {id:product_id},
            success:function(res) {
                
            }
        });

    }
</script>
<script src="{{ asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
<script src="{{ asset('assets/js/custom/apps/ecommerce/catalog/save-product.js') }}"></script>
@endsection
