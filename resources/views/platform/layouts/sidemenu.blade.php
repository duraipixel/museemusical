@php
@endphp
<div class="aside-menu flex-column-fluid">
    <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
        <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true" data-kt-menu-expand="false">
            <div class="menu-item">
                <a class="menu-link" href="#">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect x="2" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="currentColor" />
                                <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Dashboard</span>
                </a>
            </div>
           @if( access()->hasAccess(['product-category', 'product-tags', 'product-labels', 'products', 'product-attribute', 'product-collection']) )
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion @if( request()->routeIs(['product-category', 'product-tags', 'product-labels', 'products','products.*', 'product-attribute', 'product-collection'])) hover show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z" fill="currentColor" />
                                <path opacity="0.3" d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z" fill="currentColor" />
                                <path opacity="0.3" d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z" fill="currentColor" />
                                <path d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z" fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Products</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion menu-active-bg">
                    @if( access()->hasAccess(['product-category']) )
                    <div class="menu-item">
                        <a class="menu-link @if(  request()->routeIs(['product-category'])) active @endif" href="{{ route('product-category') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Product Categories</span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['product-tags']) )
                    <div class="menu-item">
                        <a class="menu-link @if(  request()->routeIs(['product-tags'])) active @endif" href="{{ route('product-tags') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Product Tags</span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['product-labels']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['product-labels'])) active @endif" href="{{ route('product-labels') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Product Labels</span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['products']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['products', 'products.*'])) active @endif" href="{{ route('products') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Products</span>
                        </a>
                    </div>
                    @endif
                    {{-- <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Product Groups</span>
                        </a>
                    </div> --}}
                    @if( access()->hasAccess(['product-collection']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['product-collection'])) active @endif" href="{{ route('product-collection') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Product Collection</span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['product-attribute']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['product-attribute'])) active @endif" href="{{ route('product-attribute') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Product Attributes</span>
                        </a>
                    </div>
                    @endif                 
                </div>
            </div>
            @endif
            @if( access()->hasAccess('customer') )
            <div class="menu-item">
                <a class="menu-link @if(  request()->routeIs(['customer'])) active @elseif( request()->routeIs(['customer.view'])) active  @endif" href="{{ route('customer') }}">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M21 18.3V4H20H5C4.4 4 4 4.4 4 5V20C10.9 20 16.7 15.6 19 9.5V18.3C18.4 18.6 18 19.3 18 20C18 21.1 18.9 22 20 22C21.1 22 22 21.1 22 20C22 19.3 21.6 18.6 21 18.3Z" fill="currentColor" />
                                <path d="M22 4C22 2.9 21.1 2 20 2C18.9 2 18 2.9 18 4C18 4.7 18.4 5.29995 18.9 5.69995C18.1 12.6 12.6 18.2 5.70001 18.9C5.30001 18.4 4.7 18 4 18C2.9 18 2 18.9 2 20C2 21.1 2.9 22 4 22C4.8 22 5.39999 21.6 5.79999 20.9C13.8 20.1 20.1 13.7 20.9 5.80005C21.6 5.40005 22 4.8 22 4Z" fill="currentColor" />
                             </svg>
                        </span>
                    </span>
                    <span class="menu-title">Customer</span>
                </a>
            </div>
            @endif
            @if( access()->hasAccess(['coupon', 'discount']) )
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion mb-1 @if( request()->routeIs(['coupon', 'discount'])) hover show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M4.05424 15.1982C8.34524 7.76818 13.5782 3.26318 20.9282 2.01418C21.0729 1.98837 21.2216 1.99789 21.3618 2.04193C21.502 2.08597 21.6294 2.16323 21.7333 2.26712C21.8372 2.37101 21.9144 2.49846 21.9585 2.63863C22.0025 2.7788 22.012 2.92754 21.9862 3.07218C20.7372 10.4222 16.2322 15.6552 8.80224 19.9462L4.05424 15.1982ZM3.81924 17.3372L2.63324 20.4482C2.58427 20.5765 2.5735 20.7163 2.6022 20.8507C2.63091 20.9851 2.69788 21.1082 2.79503 21.2054C2.89218 21.3025 3.01536 21.3695 3.14972 21.3982C3.28408 21.4269 3.42387 21.4161 3.55224 21.3672L6.66524 20.1802L3.81924 17.3372ZM16.5002 5.99818C16.2036 5.99818 15.9136 6.08615 15.6669 6.25097C15.4202 6.41579 15.228 6.65006 15.1144 6.92415C15.0009 7.19824 14.9712 7.49984 15.0291 7.79081C15.0869 8.08178 15.2298 8.34906 15.4396 8.55884C15.6494 8.76862 15.9166 8.91148 16.2076 8.96935C16.4986 9.02723 16.8002 8.99753 17.0743 8.884C17.3484 8.77046 17.5826 8.5782 17.7474 8.33153C17.9123 8.08486 18.0002 7.79485 18.0002 7.49818C18.0002 7.10035 17.8422 6.71882 17.5609 6.43752C17.2796 6.15621 16.8981 5.99818 16.5002 5.99818Z" fill="currentColor" />
                                <path d="M4.05423 15.1982L2.24723 13.3912C2.15505 13.299 2.08547 13.1867 2.04395 13.0632C2.00243 12.9396 1.9901 12.8081 2.00793 12.679C2.02575 12.5498 2.07325 12.4266 2.14669 12.3189C2.22013 12.2112 2.31752 12.1219 2.43123 12.0582L9.15323 8.28918C7.17353 10.3717 5.4607 12.6926 4.05423 15.1982ZM8.80023 19.9442L10.6072 21.7512C10.6994 21.8434 10.8117 21.9129 10.9352 21.9545C11.0588 21.996 11.1903 22.0083 11.3195 21.9905C11.4486 21.9727 11.5718 21.9252 11.6795 21.8517C11.7872 21.7783 11.8765 21.6809 11.9402 21.5672L15.7092 14.8442C13.6269 16.8245 11.3061 18.5377 8.80023 19.9442ZM7.04023 18.1832L12.5832 12.6402C12.7381 12.4759 12.8228 12.2577 12.8195 12.032C12.8161 11.8063 12.725 11.5907 12.5653 11.4311C12.4057 11.2714 12.1901 11.1803 11.9644 11.1769C11.7387 11.1736 11.5205 11.2583 11.3562 11.4132L5.81323 16.9562L7.04023 18.1832Z" fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Deals</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    @if( access()->hasAccess(['coupon']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['coupon'])) active @endif" href="{{ route('coupon') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> Coupons </span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['discount']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['discount'])) active @endif" href="{{ route('discount') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> Discount </span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            @if( access()->hasAccess(['order-status']) )
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion mb-1">
                <span class="menu-link">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <i class="bi-chat-left fs-3"></i>
                        </span>
                    </span>
                    <span class="menu-title">Orders</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Order List</span>
                        </a>
                    </div>
                    @if( access()->hasAccess(['order-status']) )
                    <div class="menu-item">
                        <a class="menu-link" href="{{ route('order-status') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> Order Status </span>
                        </a>
                    </div>
                    @endif
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> Cancelled Order </span>
                        </a>
                    </div>
                   
                  
                </div>
            </div>
            @endif
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion mb-1">
                <span class="menu-link">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M22 7H2V11H22V7Z" fill="currentColor" />
                                <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19ZM14 14C14 13.4 13.6 13 13 13H5C4.4 13 4 13.4 4 14C4 14.6 4.4 15 5 15H13C13.6 15 14 14.6 14 14ZM16 15.5C16 16.3 16.7 17 17.5 17H18.5C19.3 17 20 16.3 20 15.5C20 14.7 19.3 14 18.5 14H17.5C16.7 14 16 14.7 16 15.5Z" fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Payments</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Payment List</span>
                        </a>
                    </div>
                </div>
            </div>
            @if( access()->hasAccess(['reports.products']) )
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion mb-1 @if( request()->routeIs(['reports.*'])) hover show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" fill="currentColor" />
                                <path d="M20 8L14 2V6C14 7.10457 14.8954 8 16 8H20Z" fill="currentColor" />
                                <path d="M10.3629 14.0084L8.92108 12.6429C8.57518 12.3153 8.03352 12.3153 7.68761 12.6429C7.31405 12.9967 7.31405 13.5915 7.68761 13.9453L10.2254 16.3488C10.6111 16.714 11.215 16.714 11.6007 16.3488L16.3124 11.8865C16.6859 11.5327 16.6859 10.9379 16.3124 10.5841C15.9665 10.2565 15.4248 10.2565 15.0789 10.5841L11.4631 14.0084C11.1546 14.3006 10.6715 14.3006 10.3629 14.0084Z" fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Reports</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Order</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="#">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> Customer </span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['reports.products.list'])) active @endif" href="{{ route('reports.products.list') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> Product </span>
                        </a>
                    </div>
                  
                </div>
            </div>
            @endif
            @if( access()->hasAccess(['tax', 'charges']) )
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion mb-1 @if( request()->routeIs(['tax', 'charges'])) hover show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <i class="bi-chat-left fs-3"></i>
                        </span>
                    </span>
                    <span class="menu-title"> Taxes & Charges </span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['tax'])) active @endif" href="{{ route('tax') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Tax</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['charges'])) active @endif" href="{{ route('charges') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Shipping Charges</span>
                        </a>
                    </div>
                </div>
            </div>
            @endif
            @if( access()->hasAccess(['banner', 'walkthroughs', 'testimonials', 'video-booking']) )
            <div class="menu-item">
                <div class="menu-content pt-8 pb-0">
                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Website</span>
                </div>
            </div>
            @endif
            @if( access()->hasAccess(['banner']) )
            <div class="menu-item">
                <a class="menu-link @if( request()->routeIs(['banner'])) active @endif" href="{{ route('banner') }}">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M21 18.3V4H20H5C4.4 4 4 4.4 4 5V20C10.9 20 16.7 15.6 19 9.5V18.3C18.4 18.6 18 19.3 18 20C18 21.1 18.9 22 20 22C21.1 22 22 21.1 22 20C22 19.3 21.6 18.6 21 18.3Z" fill="currentColor" />
                                <path d="M22 4C22 2.9 21.1 2 20 2C18.9 2 18 2.9 18 4C18 4.7 18.4 5.29995 18.9 5.69995C18.1 12.6 12.6 18.2 5.70001 18.9C5.30001 18.4 4.7 18 4 18C2.9 18 2 18.9 2 20C2 21.1 2.9 22 4 22C4.8 22 5.39999 21.6 5.79999 20.9C13.8 20.1 20.1 13.7 20.9 5.80005C21.6 5.40005 22 4.8 22 4Z" fill="currentColor" />
                             </svg>
                        </span>
                    </span>
                    <span class="menu-title">Banners</span>
                </a>
            </div>
            @endif
            @if( access()->hasAccess(['walkthroughs']) )
            <div class="menu-item">
                <a class="menu-link @if( request()->routeIs(['walkthroughs'])) active @endif" href="{{ route('walkthroughs') }}">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M11.2929 2.70711C11.6834 2.31658 12.3166 2.31658 12.7071 2.70711L15.2929 5.29289C15.6834 5.68342 15.6834 6.31658 15.2929 6.70711L12.7071 9.29289C12.3166 9.68342 11.6834 9.68342 11.2929 9.29289L8.70711 6.70711C8.31658 6.31658 8.31658 5.68342 8.70711 5.29289L11.2929 2.70711Z" fill="currentColor" />
                                <path d="M11.2929 14.7071C11.6834 14.3166 12.3166 14.3166 12.7071 14.7071L15.2929 17.2929C15.6834 17.6834 15.6834 18.3166 15.2929 18.7071L12.7071 21.2929C12.3166 21.6834 11.6834 21.6834 11.2929 21.2929L8.70711 18.7071C8.31658 18.3166 8.31658 17.6834 8.70711 17.2929L11.2929 14.7071Z" fill="currentColor" />
                                <path opacity="0.3" d="M5.29289 8.70711C5.68342 8.31658 6.31658 8.31658 6.70711 8.70711L9.29289 11.2929C9.68342 11.6834 9.68342 12.3166 9.29289 12.7071L6.70711 15.2929C6.31658 15.6834 5.68342 15.6834 5.29289 15.2929L2.70711 12.7071C2.31658 12.3166 2.31658 11.6834 2.70711 11.2929L5.29289 8.70711Z" fill="currentColor" />
                                <path opacity="0.3" d="M17.2929 8.70711C17.6834 8.31658 18.3166 8.31658 18.7071 8.70711L21.2929 11.2929C21.6834 11.6834 21.6834 12.3166 21.2929 12.7071L18.7071 15.2929C18.3166 15.6834 17.6834 15.6834 17.2929 15.2929L14.7071 12.7071C14.3166 12.3166 14.3166 11.6834 14.7071 11.2929L17.2929 8.70711Z" fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </span>
                    <span class="menu-title">History Video</span>
                </a>
            </div>
            @endif
            @if( access()->hasAccess(['newsletter']) )
            <div class="menu-item">
                <a class="menu-link @if( request()->routeIs(['newsletter'])) active @endif" href="{{ route('newsletter') }}">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M6 21C6 21.6 6.4 22 7 22H17C17.6 22 18 21.6 18 21V20H6V21Z" fill="currentColor" />
                                <path opacity="0.3" d="M17 2H7C6.4 2 6 2.4 6 3V20H18V3C18 2.4 17.6 2 17 2Z" fill="currentColor" />
                                <path d="M12 4C11.4 4 11 3.6 11 3V2H13V3C13 3.6 12.6 4 12 4Z" fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">NewsLetter</span>
                </a>
            </div>
            @endif
            @if( access()->hasAccess(['testimonials']) )
            <div class="menu-item">
                <a class="menu-link @if(  request()->routeIs(['testimonials'])) active @endif" href="{{ route('testimonials') }}">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect x="2" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="currentColor" />
                                <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Testimonial</span>
                </a>
            </div>
            @endif
            @if( access()->hasAccess(['video-booking']) )
            <div class="menu-item">
                <a class="menu-link @if(  request()->routeIs(['video-booking'])) active @endif" href="{{ route('video-booking') }}">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M21 22H14C13.4 22 13 21.6 13 21V3C13 2.4 13.4 2 14 2H21C21.6 2 22 2.4 22 3V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                    <path d="M10 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H10C10.6 2 11 2.4 11 3V21C11 21.6 10.6 22 10 22Z" fill="currentColor" />
                                </svg>
                        </span>
                    </span>
                    <span class="menu-title">Video Booking</span>
                </a>
            </div>
            @endif
            @if( access()->hasAccess(['global', 'my-profile', 'users', 'roles']) )
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion mb-1 @if( request()->routeIs(['global', 'my-profile', 'my-profile.*', 'users', 'roles'])) hover show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor" />
                                <path d="M14.854 11.321C14.7568 11.2282 14.6388 11.1818 14.4998 11.1818H14.3333V10.2272C14.3333 9.61741 14.1041 9.09378 13.6458 8.65628C13.1875 8.21876 12.639 8 12 8C11.361 8 10.8124 8.21876 10.3541 8.65626C9.89574 9.09378 9.66663 9.61739 9.66663 10.2272V11.1818H9.49999C9.36115 11.1818 9.24306 11.2282 9.14583 11.321C9.0486 11.4138 9 11.5265 9 11.6591V14.5227C9 14.6553 9.04862 14.768 9.14583 14.8609C9.24306 14.9536 9.36115 15 9.49999 15H14.5C14.6389 15 14.7569 14.9536 14.8542 14.8609C14.9513 14.768 15 14.6553 15 14.5227V11.6591C15.0001 11.5265 14.9513 11.4138 14.854 11.321ZM13.3333 11.1818H10.6666V10.2272C10.6666 9.87594 10.7969 9.57597 11.0573 9.32743C11.3177 9.07886 11.6319 8.9546 12 8.9546C12.3681 8.9546 12.6823 9.07884 12.9427 9.32743C13.2031 9.57595 13.3333 9.87594 13.3333 10.2272V11.1818Z" fill="currentColor" />
                            </svg>
                        </span>
                    </span>
                    <span class="menu-title">Authentication</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    @if( access()->hasAccess(['global']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['global'])) active @endif" href="{{ route('global') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> Global Settings </span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['my-profile']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs([ 'my-profile', 'my-profile.*' ])) active @endif" href="{{ route('my-profile') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> My Account </span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['users']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['users'])) active @endif" href="{{ route('users') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Users</span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['roles']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['roles'])) active @endif" href="{{ route('roles') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> Roles </span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            @if( access()->hasAccess(['country', 'brands', 'city', 'state', 'pincode', 'main_category', 'sub_category']) )
            <div data-kt-menu-trigger="click" class="menu-item menu-accordion @if( request()->routeIs(['country', 'brands', 'city', 'state', 'pincode', 'main_category', 'sub_category'])) hover show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z" fill="currentColor" />
                                    <path d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z" fill="currentColor" />
                                </svg>
                        </span>
                    </span>
                    <span class="menu-title">Masters</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion menu-active-bg">
                    @if( access()->hasAccess(['brands']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['brands'])) active @endif" href="{{ route('brands') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Brand</span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['city']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['city'])) active @endif" href="{{ route('city') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Cities</span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['country']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['country'])) active @endif" href="{{ route('country') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Country</span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['state']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['state'])) active @endif" href="{{ route('state') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">States</span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['pincode']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['pincode'])) active @endif" href="{{ route('pincode') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Postcodes</span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['main_category']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['main_category'])) active @endif" href="{{ route('main_category') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> Dynamic Categories </span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['sub_category']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['sub_category'])) active @endif" href="{{ route('sub_category') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> Dynamic SubCategories </span>
                        </a>
                    </div>
                    @endif
                    @if( access()->hasAccess(['email-template']) )
                    <div class="menu-item">
                        <a class="menu-link @if( request()->routeIs(['email-template'])) active @endif" href="{{ route('email-template') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title"> Email Template </span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
<div class="aside-footer flex-column-auto pt-5 pb-7 px-5" id="kt_aside_footer">
    {{-- <span class="btn-label">@ Pixel</span> --}}
</div>