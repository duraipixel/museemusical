@if( isset( $from ) && $from == 'pdf')
<style>
    table{ border-spacing: 0;width:100%; }
    table th,td {
        border:1px solid;
    }
</style>
@endif
<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>Product Name</th>
            <th>Sku</th>
            <th>Hsn No</th>
            <th>Base Price</th>
            <th>Sale Price</th>
            <th>Sale Start Date</th>
            <th>Sale End Date</th>
            <th>Status</th>
            <th>Quantity</th>
            <th>Video Shopping</th>
            <th>Stock Status</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Tag</th>
            <th>Label</th>
            <th>Featured</th>
            <th>Description</th>
            <th>Technical Information</th>
            <th>Feature Information</th>
            <th>Specification</th>
            <th>Added At</th>
            <th>Added By</th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->id }} </td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->sku }}</td>
                <td>{{ $item->hsn_code ?? '' }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->sale_price }}</td>
                <td>{{ $item->sale_start_date }}</td>
                <td>{{ $item->sale_end_date }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->has_video_shopping }}</td>
                <td>{{ $item->stock_status }}</td>
                <td>{{ $item->productBrand->brand_name ?? '' }}</td>
                <td>{{ $item->productCategory->name ?? '' }}</td>
                <td>{{ $item->productTag->category_name ?? '' }}</td>
                <td>{{ $item->productLabel->category_name ?? '' }}</td>
                <td>{{ ( isset( $item->is_featured ) && $item->is_featured == 1 ) ? 'Yes' : 'No' }}</td>
                <td>{{ $item->description ?? '' }}</td>
                <td>{{ $item->technical_information ?? '' }}</td>
                <td>{{ $item->feature_information ?? '' }}</td>
                <td>{{ $item->specification ?? '' }} </td>
                <td>{{ $item->userInfo->name ?? '' }}</td>
                <td>{{ $item->created_at }}</td>

            </tr>
            @endforeach
        @endif
    </tbody>
</table>