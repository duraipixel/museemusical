
<div class="card-body pt-0">
    
    <select name="category_id" id="category_id" aria-label="Select a Category" data-control="select2" data-placeholder="Select a Category..." class="form-select mb-2">
        {{-- <option value=""></option> --}}
        @isset($productCategory)
            @foreach ($productCategory as $item)
                <option value="{{ $item->id }}" @if( isset( $category_id ) && $category_id == $item->id ) selected @endif>{{ $item->name }} - {{ $item->parent->name ?? 'Parent' }} </option>
            @endforeach
        @endisset
    </select>
</div>
<script>
    // $('#category_id').select2();
</script>