@if( isset($info->productAttributes) && !empty( $info->productAttributes))

    @foreach ($info->productAttributes as $attr)
   
        <div class="form-group d-flex flex-wrap gap-5 childRow" >
            <!--begin::Select2-->
            <div class="w-100 w-md-200px">
                <select class="form-select product-attr-select required" name="filter_variation[]" aria-label="Select a Attribute" data-control="select2" data-placeholder="Select a Attribute..."  >
                    <option></option>
                    @foreach ($attributes as $item)
                        <option value="{{ $item->id }}" 
                            @if( isset($attr->product_attribute_set_id) && $attr->product_attribute_set_id == $item->id ) selected @endif>
                            {{ $item->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!--end::Select2-->
            <!--begin::Input-->
            <input type="text" class="form-control mw-100 w-200px required" name="filter_variation_value[]" value="{{ $attr->attribute_values ?? ''}}" placeholder="Values" required />
            <!--end::Input-->
            <button type="button" data-repeater-delete="" class="btn btn-sm btn-icon btn-light-danger removeRow"  >
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
        <script>

            setTimeout(() => {
                $('.product-attr-select').select2();    
            }, 200);
            
        </script>
    @endforeach
@else
<div class="form-group d-flex flex-wrap gap-5 childRow" >
    <!--begin::Select2-->
    <div class="w-100 w-md-200px">
        <select class="form-select product-attr-select required" name="filter_variation[]"  aria-label="Select a Attribute" data-control="select2" data-placeholder="Select a Attribute..."  >
            <option></option>
            @foreach ($attributes as $item)
                <option value="{{ $item->id }}">{{ $item->title }}</option>
            @endforeach
        </select>
    </div>
    <!--end::Select2-->
    <!--begin::Input-->
    <input type="text" class="form-control mw-100 w-200px required" name="filter_variation_value[]" placeholder="Values" required />
    <!--end::Input-->
    <button type="button" data-repeater-delete="" class="btn btn-sm btn-icon btn-light-danger removeRow"  >
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
<script>

    setTimeout(() => {
        $('.product-attr-select').select2();    
    }, 200);
    
</script>
@endif
