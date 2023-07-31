<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<body>
    <style>
        body {
            border: 1px solid #ddd;
        }

        table td {
            font-size: 11px;
        }

        .header-table,
        .item-table {
            width: 100%;
        }

        .header-table td,
        th {
            border: 1px solid #ddd;
            border-collapse: collapse;
            padding: 5px;
        }

        .item-table td,
        .item-table th {
            border: 1px solid #ddd;
            border-collapse: collapse;
            padding: 5px;
        }

        .total-amount-table td,
        .total-amount-table th {
            padding: 5px;
        }

        .no-border td,
        th {
            border: none;
            width: 100%;
            font-size: 13px;
            color: #000000;
        }

        .w-50 {
            width: 50%;
        }

        .w-30 {
            width: 50%;
        }

        .w-40 {
            width: 50%;
        }

        .p-5 {
            padding: 5px;
        }
    </style>
    <table class="header-table" cellspacing="0" padding="0">

        {{-- {{ dd($globalInfo) }} --}}
        <tr>
            <td colspan="2">
                <table class="no-border" style="width: 100%">
                    <tr>
                        <td class="w-30"> <span><img src="{{ asset('assets/logo/logo.png') }}" alt=""
                                    height="100"></span> </td>
                        <td class="w-30">
                            <h3> {{ $globalInfo->site_name }} </h3>
                            <div> {{ $globalInfo->address }} </div>
                            <div> {{ $globalInfo->site_email }} </div>
                            <div> {{ $globalInfo->site_mobile_no }} </div>
                            {{-- <div> GSTIN: 33334DS22SD34FHJ63A </div> --}}
                        </td>
                        <td class="w-40">
                            <h1>Tax Invoice</h1>
                        </td>
                    </tr>
                </table>
            </td>

        </tr>
        <tr>
            <td>
                <table class="no-border" style="width: 100%">
                    <tr>
                        <td class="w-50">
                            <h3> Billing Details </h3>
                            <div><b>{{ $order_info->billing_name }}</b></div>
                            <div>{{ $order_info->billing_address_line1 }}</div>
                            <div>{{ $order_info->billing_city }}</div>
                            <div>{{ $order_info->billing_state }}</div>
                            <div>{{ $order_info->billing_mobile_no }}</div>
                            <div>{{ $order_info->billing_email }}</div>
                            <div>{{ $order_info->billing_post_code }}</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td>

                <table class="no-border">
                    <tr>
                        <td class="w-30"> Invoice Date </td>
                        <td class="w-30"> {{ date('d/m/Y H:i A', strtotime($order_info->created_at)) }} </td>
                    </tr>
                    <tr>
                        <td class="w-30"> Invoice No </td>
                        <td class="w-30"> {{ $order_info->order_no }} </td>
                    </tr>
                    <tr>
                        <td class="w-30"> Payment Status </td>
                        <td class="w-30"> {{ $order_info->payment_status ?? 'Pending' }} </td>
                    </tr>
                    @if ($order_info->is_cod == 'yes')
                        <tr>
                            <td class="w-30"> Delivery Type </td>
                            <td class="w-30"> Cash on Delivery </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>


    </table>

    @if (isset($order_info->orderItems) && !empty($order_info->orderItems))
        @php
            $check_discount = 0;
        @endphp
        @foreach ($order_info->orderItems as $item)
            @if ($item->discount_percentage > 0)
                @php
                    $check_discount = 1;
                @endphp
            @endif
        @endforeach
    @endif
    <table class="item-table" cellspacing="0" padding="0">
        <tr>
            <th style="width: 10px;" rowspan="2">S.No</th>
            <th rowspan="2" style="width: 78%"> Items</th>
            <th rowspan="2" style="width: 90px;"> HSN</th>
            <th rowspan="2"> Reference</th>
            <th rowspan="2"> Qty</th>
            <th rowspan="2"> Rate </th>
            @if ($check_discount == 1)
                <th rowspan="2"> Discount </th>
            @endif
            <th colspan="2"> CGST </th>
            <th colspan="2"> SGST </th>
            <th rowspan="2"> Amount </th>
        </tr>
        <tr>
            <th>%</th>
            <th>Amt</th>
            <th>%</th>
            <th>Amt</th>
        </tr>
        @if (isset($order_info->orderItems) && !empty($order_info->orderItems))
            @php
                $i = 1;
                // dd( $order_info->orderItems);
            @endphp
            @foreach ($order_info->orderItems as $item)
                <tr>
                    <td>{{ $i }}</td>
                    <td>
                        {{ $item->product_name }}
                    </td>
                    <td> {{ $item->hsn_code }} </td>
                    <td> {{ $item->sku }} </td>
                    <td> {{ $item->quantity }} nos</td>
                    <td> {{ number_format($item->price, 2) }} </td>
                    @if ($check_discount == 1)
                        <td>{{ $item->discount_percentage }}%</td>
                    @endif
                    <td>{{ $item->tax_percentage / 2 }}%</td>
                    <td>{{ number_format($item->tax_amount / 2, 2) }}</td>
                    <td>{{ $item->tax_percentage / 2 }}%</td>
                    <td>{{ number_format($item->tax_amount / 2, 2) }}</td>
                    <td>{{ number_format($item->sub_total, 2) }}</td>
                </tr>
                @php
                    $i++;
                @endphp
            @endforeach
        @endif
    </table>
    <table class="item-table" cellspacing="0" padding="0">
        <tr>
            <td style="padding-top:10px;width:50%;border-bottom:none;">
                <div>
                    <label for="">Total in words </label>
                </div>
                <div>
                    <b>{{ ucwords(getIndianCurrency($order_info->amount)) }}</b>
                </div>
                <div style="margin-top: 10px;">
                    Thank you for the payment. You just made our day
                </div>
            </td>
            <td style="width: 50%;">
                <table class="no-border" cellspacing="0" padding="0" style="width: 100%;">
                    <tr>
                        <td style="text-align: right">
                            <div>Sub Total </div>


                            <small>(Tax inclusive)</small>
                        </td>
                        <td style="text-align: right"><span style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>
                            {{ number_format($order_info->sub_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: right">Tax (%{{ number_format($order_info->tax_percentage) }}) </td>
                        <td style="text-align: right"><span
                                style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{ number_format($order_info->tax_amount, 2) }}
                        </td>
                    </tr>
                    @if ($order_info->coupon_amount > 0)
                        <tr>
                            <td style="text-align: right">
                                <div>Coupon Amount </div>
                                <small>( {{ $order_info->coupon_code }})</small>
                            </td>
                            <td style="text-align: right"><span
                                    style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{ number_format($order_info->coupon_amount, 2) }}
                            </td>
                        </tr>
                    @endif

                    @if ($order_info->discount_amount > 0)
                        <tr>
                            <td style="text-align: right">
                                <div>Discount Amount </div>
                            </td>
                            <td style="text-align: right"><span
                                    style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{ number_format($order_info->discount_amount, 2) }}
                            </td>
                        </tr>
                    @endif
                    @if ($order_info->shipping_amount > 0)
                        <tr>
                            <td style="text-align: right">
                                <div>Shipping Fee </div>
                                <small>( {{ $order_info->shipping_type }})</small>
                            </td>
                            <td style="text-align: right"><span
                                    style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{ number_format($order_info->shipping_amount, 2) }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="text-align: right;font-weight:700;font-size:15px;">Total</td>
                        <td style="text-align: right;font-weight:700;font-size:15px;">
                            <span
                                style="font-family: DejaVu Sans; sans-serif;">&#8377;</span>{{ number_format($order_info->amount, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2 " style="text-align: center;border-top:1px solid #ddd">
                            <div style="margin-top: 100px">Authorized Signature</div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
