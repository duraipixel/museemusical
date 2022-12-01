<!DOCTYPE html>
<html>
<body>
   <style>
    body { border: 1px solid #ddd; }
    table td {
        font-size: 11px;
    }
        .header-table, .item-table {
            width: 100%; 
        }
        
        .header-table td,th {
           border: 1px solid #ddd;
           border-collapse: collapse;
           padding: 5px;
        }

        .item-table td,.item-table th {
           border: 1px solid #ddd;
           border-collapse: collapse;
           padding: 5px;
        }

        .total-amount-table td,.total-amount-table th {
           padding: 5px;
        }

        .no-border td, th {
            border:none;
            width: 100%;
            font-size: 13px;
            color: #000000;
        }
        .w-50 {
            width:50%;
        }
        .w-30 {
            width:50%;
        }
        .w-40 {
            width:50%;
        }
        .p-5 {
            padding: 5px;
        }
   </style>
    <table class="header-table" cellspacing="0" padding="0">
       <tr>
            <td colspan="2">
                <table class="no-border" style="width: 100%">
                    <tr>
                        <td class="w-30"> <span><img src="{{ public_path('assets/logo/logo.png') }}" alt="" height="100"></span> </td>
                        <td class="w-30" >
                            <h3> Musee Musical Pvt Ltd </h3>
                            <div> No 73 </div>
                            <div> Anna salai </div>
                            <div> Chennai 600023 </div>
                            <div> GSTIN: 33334DS22SD34FHJ63A </div>
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
                            <div><b>MR. XYX Man </b></div>
                            <div>NO: 3, 2nd street</div>
                            <div>Chennai</div>
                            <div>Tamilnadu</div>
                            <div>979797979798</div>
                            <div>Customer@mail.com</div>
                        </td>
                    </tr>
                </table>
           </td>
           <td >
                
                <table class="no-border">
                    <tr>
                        <td class="w-30"> Invoice Date </td>
                        <td class="w-30"> 25/02/2022 </td>
                    </tr>
                    <tr>
                        <td class="w-30"> Invoice No </td>
                        <td class="w-30">  INV-TN-201555550 </td>
                    </tr>
                    <tr>
                        <td class="w-30"> Payment Status </td>
                        <td class="w-30"> Paid </td>
                    </tr>
                </table>
           </td>
       </tr>
       
       
   </table>
   <table class="item-table" cellspacing="0" padding="0">
        <tr>
           <th style="width: 10px;" rowspan="2">S.No</th>
           <th rowspan="2" style="width: 140px;"> Items</th>
           <th rowspan="2"> HSN</th>
           <th rowspan="2"> Reference</th>
           <th rowspan="2"> Qty</th>
           <th rowspan="2"> Rate </th>
           <th rowspan="2"> Discount </th>
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

        <tr>
            <td>1</td>
            <td>
                Electronic Keyboard 
                Yamaha PSR E473 Digital 
                Keyboar
            </td>
            <td>15200</td>
            <td> MMPL/21/9/22-1 </td>
            <td> 1 nos</td>
            <td>2000.00 </td>
            <td>10%</td>
            <td>5%</td>
            <td>1358.20</td>
            <td>5%</td>
            <td>1358.20</td>
            <td>17991</td>
        </tr>
        <tr>
            <td>2</td>
            <td>
                Electronic Keyboard 
                Yamaha PSR E473 Digital 
                Keyboar
            </td>
            <td>15200</td>
            <td> MMPL/21/9/22-1 </td>
            <td> 1 nos</td>
            <td>2000.00 </td>
            <td>10%</td>
            <td>5%</td>
            <td>1358.20</td>
            <td>5%</td>
            <td>1358.20</td>
            <td>17991</td>
        </tr>
        <tr>
            <td>3</td>
            <td>
                Electronic Keyboard 
                Yamaha PSR E473 Digital 
                Keyboar
            </td>
            <td>15200</td>
            <td> MMPL/21/9/22-1 </td>
            <td> 1 nos</td>
            <td>2000.00 </td>
            <td>10%</td>
            <td>5%</td>
            <td>1358.20</td>
            <td>5%</td>
            <td>1358.20</td>
            <td>17991</td>
        </tr>
       
   </table>
   <table class="item-table" cellspacing="0" padding="0" >
        <tr>
            <td style="padding-top:10px;width:50%;border-bottom:none;" >
                <div>
                    <label for="">Total in words </label>
                </div>
                <div>
                    <b>Indian Rupee Nineteen Thousand Four Hundred Thirty-One Only</b>
                </div>
                <div>
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
                        <td style="text-align: right">19,431.00</td>
                    </tr>
                    <tr>
                        <td style="text-align: right">CGST9(9%) </td>
                        <td style="text-align: right">1,482.00</td>
                    </tr>
                    <tr>
                        <td style="text-align: right">SGST9(9%) </td>
                        <td style="text-align: right">1,482.00</td>
                    </tr>
                    <tr>
                        <td style="text-align: right;font-weight:700;font-size:15px;">Total</td>
                        <td style="text-align: right;font-weight:700;font-size:15px;">₹19,431.00</td>
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