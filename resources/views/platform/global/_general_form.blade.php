<form id="kt_account_global_form" class="form" enctype="multipart/form-data">
    @csrf
    <div class="card-body border-top p-9">
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Favicon</label>
            <div class="col-lg-8">
                <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('../assets/media/svg/avatars/blank.svg')">
                    <div class="image-input-wrapper w-125px h-125px" style="background-image: url(../assets/media/logos/favicon.ico)"></div>
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <input type="file" name="favicon" accept=".png, .jpg, .jpeg" />
                        <input type="hidden" name="favicon_remove" />
                    </label>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                        <i class="bi bi-x fs-2"></i>
                    </span>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                        <i class="bi bi-x fs-2"></i>
                    </span>
                </div>
                <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
            </div>
        </div>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Logo</label>
            <div class="col-lg-8">
                <div class="image-input image-input-outline" data-kt-image-input="true" style="background-image: url('../assets/media/svg/avatars/blank.svg')">
                    <div class="image-input-wrapper w-125px h-125px" style="background-image: url(../assets/media/avatars/300-1.jpg)"></div>
                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                        <i class="bi bi-pencil-fill fs-7"></i>
                        <input type="file" name="logo" accept=".png, .jpg, .jpeg" />
                        <input type="hidden" name="logo_remove" />
                    </label>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                        <i class="bi bi-x fs-2"></i>
                    </span>
                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                        <i class="bi bi-x fs-2"></i>
                    </span>
                </div>
                <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
            </div>
        </div>
       
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label required fw-bold fs-6">Site Name</label>
            <div class="col-lg-8 fv-row">
                <input type="text" name="site_name" class="form-control form-control-lg form-control-solid" placeholder="Site name" value="" />
            </div>
        </div>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">
                <span class="required">Contact Phone</span>
                {{-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Phone number must be active"></i> --}}
            </label>
            <div class="col-lg-8 fv-row">
                <input type="tel" name="site_mobile_no" class="form-control form-control-lg form-control-solid" placeholder="Phone number" value="044 3276 454 935" />
            </div>
        </div>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">
                <span class="required">Contact Email</span>
                {{-- <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Phone number must be active"></i> --}}
            </label>
            <div class="col-lg-8 fv-row">
                <input type="email" name="site_email" class="form-control form-control-lg form-control-solid" placeholder="Site Email" value="" />
            </div>
        </div>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Company Site</label>
            <div class="col-lg-8 fv-row">
                <input type="text" name="website" class="form-control form-control-lg form-control-solid" placeholder="Company website" value="" />
            </div>
        </div>
        <div class="row mb-6">
            <label class="col-lg-4 col-form-label fw-bold fs-6">Copyrights</label>
            <div class="col-lg-8 fv-row">
                <input type="text" name="copyrights" class="form-control form-control-lg form-control-solid" placeholder="Copyrights" value="" />
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