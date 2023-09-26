<div class="d-flex align-items-stretch flex-shrink-0">
    <!--begin::Search-->
    <div class="d-flex align-items-stretch ms-1 ms-lg-3">
        <!--begin::Search-->
        {{-- @include('platform.layouts.parts.common._header_search_tool') --}}
        <!--end::Search-->
    </div>
    <!--end::Search-->
    <!--begin::Activities-->
    <div class="d-flex align-items-center ms-1 ms-lg-3">
        <!--begin::Drawer toggle-->
        {{-- @include('platform.layouts.parts.common._header_order_notification') --}}
        <!--end::Drawer toggle-->
    </div>
    <!--end::Activities-->
    
    <!--begin::User menu-->
    <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
        @include('platform.layouts.parts.common._user_toggle_menu')
    </div>
    <!--end::User menu-->
</div>