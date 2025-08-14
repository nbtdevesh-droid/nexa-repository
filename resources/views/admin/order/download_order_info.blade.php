<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding: 20px;
            background-color: #ff8008;
            color: #ffffff;
            border-radius: 8px 8px 0 0;
        }

        .header img {
            max-width: 150px;
            height: auto; /* Keep aspect ratio */
        }

        .content {
            /*padding: 20px; */
        }

        h2 {
            margin: 10px 0;
        }

        table {
            margin-bottom: 20px;
            border-collapse: collapse;
            width: 100%; /* Make table full width */
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            border-bottom: 1px solid #dddddd;
        }

        .table-bordered {
            border: 1px solid #dddddd;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #dddddd;
            text-align: center;
        }

        .total-section {
            text-align: right;
            padding: 20px 0;
        }

        .footer {
            background-color: #E5EEF4;
            padding: 10px;
            text-align: center;
        }

        .footer p {
            margin: 0;
            color: #09405E;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .header {
                background-color: #ff8008 !important;
            }

            th, td {
                color: #000000 !important;
            }

            img {
                display: inline !important;
                max-width: 100%;
                height: auto;
            }
        }
        @font-face {
            font-family: 'DejaVu Sans';
            src: url("{{ public_path('fonts/DejaVuSans.ttf') }}") format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
        }

        table, th, td {
            font-family: 'DejaVu Sans', sans-serif;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @php
                $imagePath = public_path('admin-assets/dist/img/logo_dashboard.png');
                $imageData = base64_encode(file_get_contents($imagePath));
                $src = 'data:'.mime_content_type($imagePath).';base64,'.$imageData;
            @endphp
            <img src="{{ $src }}" alt="Logo">
        </div>
        <div class="content">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <p><strong>Invoice No</strong></p>
                        <table class="invoice-order-info">
                            <tr>
                                <th>Order No:</th>
                                <td>{{ $order_info->order_id }}</td>
                            </tr>
                            <tr>
                                <th>Order Status:</th>
                                <td>{{ $order_info->order_status }}</td>
                            </tr>
                            <tr>
                                <th>Deliver Option:</th>
                                <td>{{ $order_info->delivery_option }}</td>
                            </tr>
                            <tr>
                                <th>Payment Method:</th>
                                <td>
                                    @if($order_info->payment_mode == 'cod')
                                        Cash on Delivery
                                    @else
                                        Online Payment
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Order Date:</th>
                                <td>{{ $order_info->created_at->format('d F, Y') }}</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        @php
                            $address = json_decode($order_info->shiping_address_id, true);
                            $warehouse = json_decode($order_info->warehouse_id, true);
                        @endphp
                        <p><strong>Shipping Address</strong></p>
                        <p>{{ $address['name'] }}<br>{{ $address['country_code'] }} {{ $address['phone'] }}<br>{{ $address['address'] }}<br>{{ $address['city'] }}, {{ $address['state'] }}<br>{{ $address['country'] }} - {{ $address['zip_code'] }}</p>
                        @if($order_info->delivery_option == 'Pickup' && !is_null($order_info->warehouse_id))
                            @php
                                $warehouse = json_decode($order_info->warehouse_id, true);
                            @endphp
                            <p><strong>Warehouse Address</strong></p>
                            <p>
                                {{ $warehouse['warehouse_name'] }}<br>
                                {{ $warehouse['street_address'] }}<br>
                                {{ $warehouse['city'] }}, {{ $warehouse['state'] }}<br>
                                {{ $warehouse['country'] }} - {{ $warehouse['zip_code'] }}
                            </p>
                            <p><strong>Warehouse Contact Details:</strong></p>
                            <p>
                                {{ $warehouse['contact_name'] }}<br>
                                {{ $warehouse['contact_email'] }}<br>
                                {{ $warehouse['country_code'] }} {{ $warehouse['contact_number'] }}
                            </p>
                        @endif
                    </td>
                </tr>
            </table>
            <h2>Order Details</h2>
            <table class="table-bordered">
                <thead>
                    <tr>
                        <th>Item Image</th>
                        <th>Item Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $subTotal = 0;
                        $total = 0;
                    @endphp
                    @php $order_item_information = json_decode($order_info->product_complete_details, true); @endphp
                    @foreach($order_item_information as $product_detail)
                        {{-- @php
                            $imagePath1 = public_path('admin-assets/assets/img/product/feature_img/' . $product_detail['feature_image']);
                            if($imagePath1){
                                $imageData1 = base64_encode(file_get_contents($imagePath1));
                                $src1 = 'data:'.mime_content_type($imagePath1).';base64,'.$imageData1;
                            }
                        @endphp --}}
                        <tr>
                            <td></td>
                            <td>{{ $product_detail['product_name'] }}</td>
                            <td>₦{{ $product_detail['purchase_price'] }}</td>
                            <td>{{ $product_detail['purchase_quantity'] }}</td>
                            <td>₦{{ $product_detail['purchase_total_price'] }}</td>
                        </tr>
                        
                        @php
                            $subTotal += ($product_detail['regular_price'] ?? 0) * ($product_detail['purchase_quantity']);
                            $total += $product_detail['purchase_total_price'];
                        @endphp
                    @endforeach
                    @php
                        $saveTotal = $subTotal - $total;
                    @endphp
                </tbody>
            </table>
           
            <div class="total-section">
                <table>
                    <tr>
                        <th>Sub Total Price:</th>
                        <td>₦{{ number_format($subTotal, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Save:</th>
                        <td>₦{{ number_format($saveTotal, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total:</th>
                        <td>₦{{ number_format($total, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Discount:</th>
                        <td>₦{{ number_format($order_info->coupon_discount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Shipping Charge:</th>
                        <td>₦{{ number_format($order_info->shipping_charges, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Price:</th>
                        <td>₦{{ number_format($order_info->net_amount, 2) }}</td>
                    </tr>
                </table>
            </div>

        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NEXA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
