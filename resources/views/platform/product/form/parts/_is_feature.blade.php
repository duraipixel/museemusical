<div class="card-header">
    <h2>Is featured?</h2>
</div>
<div class="card-body pt-0">
    <div class="form-check form-switch form-check-custom form-check-solid fw-bold fs-6 mb-2">
        <input class="form-check-input" type="checkbox"  name="is_featured" value="1"  @if(isset( $info->is_featured) && $info->is_featured == '1') checked @endif />
    </div>
</div>