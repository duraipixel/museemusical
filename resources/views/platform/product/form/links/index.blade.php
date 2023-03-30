<!--begin::Variations-->
<div class="card card-flush py-4">
    <!--begin::Card header-->
    <div class="card-header">
        <div class="card-title w-100">
          
            <h2 class="w-100">
                Product Links

               
            </h2>

            
        </div>
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Input group-->
        <div class="" data-kt-ecommerce-catalog-add-product="auto-options">
            <!--begin::Label-->
            <!--end::Label-->
            <!--begin::Repeater-->
            <div id="kt_ecommerce_add_product_options">
                <!--begin::Form group-->
                <div class="form-group">
                    <div class="d-flex flex-column gap-3" id="formRepeaterUrl">
                        @if( isset( $info->productAllLinks) && !empty($info->productAllLinks) )
                        @foreach ($info->productAllLinks as $item)
                        <div class="form-group d-flex flex-wrap gap-5 childUrlRow" id="child-url" >
                            
                            <input type="text" name="url[]" placeholder="Url" value="{{ $item->url ?? '' }}" class="form-control mw-100 w-500px">
                            <!--end::Select2-->
                            <!--begin::Input-->
                            <select name="url_type[]" id="" class="form-control w-25">
                                <option value="video_link"  @if( isset( $item->url_type ) && $item->url_type == 'video_link') selected @endif>Video</option>
                                <option value="audio_link" @if( isset( $item->url_type ) && $item->url_type == 'audio_link') selected @endif>Audio</option>
                            </select>
                            <!--end::Input-->
                            <button type="button" data-repeater-delete="" class="btn btn-sm btn-icon btn-light-danger removeUrlRow"  >
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor" />
                                        <rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </button>
                        </div>
                        @endforeach
                        @else
                        <div class="form-group d-flex flex-wrap gap-5 childUrlRow" id="child-url" >
                            
                            <input type="text" name="url[]" placeholder="Url" class="form-control mw-100 w-500px">
                            <!--end::Select2-->
                            <!--begin::Input-->
                            <input type="text" class="form-control mw-100 w-250px" name="url_type[]" placeholder="url type" />
                            <select name="url_type[]" id="" class="form-control w-25">
                                <option value="video_link">Video</option>
                                <option value="audio_link">Audio</option>
                            </select>
                            <!--end::Input-->
                            <button type="button" data-repeater-delete="" class="btn btn-sm btn-icon btn-light-danger removeUrlRow"  >
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor" />
                                        <rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <!--end::Form group-->
                <!--begin::Form group-->
                <div class="form-group mt-5">
                    <button type="button" class="btn btn-sm btn-light-primary" onclick="return addLinks()">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr087.svg-->
                    <span class="svg-icon svg-icon-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect opacity="0.5" x="11" y="18" width="12" height="2" rx="1" transform="rotate(-90 11 18)" fill="currentColor" />
                            <rect x="6" y="11" width="12" height="2" rx="1" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->Add Links</button>
                </div>
                <!--end::Form group-->
            </div>
            <!--end::Repeater-->
        </div>
        <!--end::Input group-->
    </div>
    <!--end::Card header-->
</div>
<!--end::Variations-->

<script>
    // $("body").on("click", ".removeUrlRow", function () {
    //     $(this).parents(".childUrlRow").remove();
    // })
</script>
