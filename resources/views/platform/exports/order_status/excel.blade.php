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
            <th>Added Date</th>
            <th>Status Name</th>
            <th>Description</th>
            <th>Order</th>
            <th>Status</th>
            <th> Added By </th>
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->created_at }}</td>
                <td>{{ $item->status_name }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->order }}</td>
                <td>{{  $item->status }}</td>
                <td>{{ $item->users_name }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>