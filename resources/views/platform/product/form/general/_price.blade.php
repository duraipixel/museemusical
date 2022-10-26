<div class="card-header">
    <div class="card-title">
        <h2>Pricing</h2>
    </div>
</div>

<div class="card-body pt-0">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-10">
                <label class="required form-label">Base Price</label>
                <input type="text" name="base_price" class="form-control mb-2" placeholder="Product Price" value="" />
                <div class="text-muted fs-7">Set the product price.</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-10">
                <label class="form-label">Sale Price</label>
                <input type="text" name="sale_price" class="form-control mb-2" placeholder="Sale Price" value="" />
            </div>
        </div>
    </div>
    <div class="fv-row mb-10">
        <label class="fs-6 fw-bold mb-2">Discount Type
        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Select a discount type that will be applied to this product"></i></label>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
            <div class="col">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default active d-flex text-start p-6" data-kt-button="true">
                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                        <input class="form-check-input" type="radio" name="discount_option" value="1" checked="checked" />
                    </span>
                    <span class="ms-5">
                        <span class="fs-4 fw-bolder text-gray-800 d-block">No Discount</span>
                    </span>
                </label>
            </div>
            <div class="col">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default d-flex text-start p-6" data-kt-button="true">
                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                        <input class="form-check-input" type="radio" name="discount_option" value="2" />
                    </span>
                    <span class="ms-5">
                        <span class="fs-4 fw-bolder text-gray-800 d-block">Percentage %</span>
                    </span>
                </label>
            </div>
            <div class="col">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default d-flex text-start p-6" data-kt-button="true">
                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                        <input class="form-check-input" type="radio" name="discount_option" value="3" />
                    </span>
                    <span class="ms-5">
                        <span class="fs-4 fw-bolder text-gray-800 d-block">Fixed Price</span>
                    </span>
                </label>
            </div>
        </div>
    </div>
   
    <div class="d-none mb-10 fv-row" id="kt_ecommerce_add_product_discount_percentage">
        <label class="form-label">Set Discount Percentage1</label>
        <div class="d-flex flex-column text-center mb-5">
            <div class="d-flex align-items-start justify-content-center mb-7">
                <span class="fw-bolder fs-3x" id="kt_ecommerce_add_product_discount_label">0</span>
                <span class="fw-bolder fs-4 mt-1 ms-2">%</span>
            </div>
            <div id="kt_ecommerce_add_product_discount_slider" class="noUi-sm"></div>
        </div>
        <div class="text-muted fs-7">Set a percentage discount to be applied on this product.</div>
    </div>
    <div class="d-none mb-10 fv-row" id="kt_ecommerce_add_product_discount_fixed">
        <label class="form-label">Fixed Discounted Price</label>
        <input type="text" name="dicsounted_price" class="form-control mb-2" placeholder="Discounted price" />
        <div class="text-muted fs-7">Set the discounted product price. The product will be reduced at the determined fixed price</div>
    </div>
    <hr>
    <div class="fv-row mb-10">
        <label class="fs-6 fw-bold mb-2">Tax 
        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Select a discount type that will be applied to this product"></i></label>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-1 row-cols-xl-3 g-9" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button='true']">
            <div class="col">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default active d-flex text-start p-6" data-kt-button="true">
                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                        <input class="form-check-input" type="radio" name="discount_tax_option" value="1" checked="checked" />
                    </span>
                    <span class="ms-5">
                        <span class="fs-4 fw-bolder text-gray-800 d-block">No Tax</span>
                    </span>
                </label>
            </div>
            <div class="col">
                <label class="btn btn-outline btn-outline-dashed btn-outline-default d-flex text-start p-6" data-kt-button="true">
                    <span class="form-check form-check-custom form-check-solid form-check-sm align-items-start mt-1">
                        <input class="form-check-input" type="radio" name="discount_tax_option" value="2" />
                    </span>
                    <span class="ms-5">
                        <span class="fs-4 fw-bolder text-gray-800 d-block">Tax %</span>
                    </span>
                </label>
            </div>
        </div>
    </div>
    <div class="d-none d-flex flex-wrap gap-5" id="kt_ecommerce_add_product_tax_fixed">
        <div class="fv-row w-100 flex-md-root">
            <label class="form-label">Tax Amount (%)</label>
            <input type="text" class="form-control mb-2" value="" />
            <div class="text-muted fs-7">Set the product Tax about.</div>
        </div>
    </div>
</div>