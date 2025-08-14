<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Received</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
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
            background: linear-gradient(0deg, rgba(255, 128, 8, 1) 0%, rgba(255, 175, 55, 1) 100%);
            color: #ffffff;
            border-radius: 8px 8px 0 0;
        }
        .content {
            padding: 20px;
        }
        .content p {
            font-size: 16px;
            color: #333333;
        }
        .content .order-details {
            margin: 0 0;
            font-size: 16px;
        }
        .order-summary {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
        }
        .order-summary p {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            padding: 10px;
              /*color: #999999;*/
            background-color: #E5EEF4;
        }
        .footer p {
            color: #09405E;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
             <img src="{{ asset('/admin-assets/dist/img/logo_dashboard.png') }}" alt="logo_dashboard" class="brand-image img-circle elevation-3">
        </div>
        <div class="content">
            <h2>Order Details</h2>
            <p>A new order has been placed on <strong>NEXA</strong>. Here are the details:</p>

            <ul>
                <li><strong>Order Number:</strong> {{ $order->order_id }}</li>
                <li><strong>Order Status:</strong> Pending</li>
                <li><strong>Delivery Option:</strong> {{ $order->delivery_option }}</li>
                <li><strong>Order Date:</strong>  @if($order->created_at) {{ $order->created_at->format('d F, Y') }} @else Not available @endif</li>
                <li><strong>Payment Method:</strong> @if($order->payment_mode == 'cod') Cash on Delivery @else Online Payment @endif</li>
                <li><strong>Customer Name:</strong> {{ $user->first_name . ' ' . $user->last_name }}</li>
                <li><strong>Customer Email:</strong> {{ $user->email }}</li>
                <li><strong>Customer Phone:</strong> {{ $user->country_code . ' ' . $user->phone }}</li>
            </ul>

            <div class="order-summary">
                <h3>Order Summary:</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 8px;">Image</th>
                            <th style="padding: 8px;">Product Name</th>
                            <th style="padding: 8px;">Quantity</th>
                            <th style="padding: 8px;">Price</th>
                            <th style="padding: 8px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $subTotal = 0;
                            $total = 0;
                        @endphp
                        @foreach(json_decode($order->product_complete_details, true) as $product)
                            <tr>
                                <td style="padding: 8px;">
                                    <img src="{{ asset('admin-assets/assets/img/product/feature_img/' . $product['feature_image']) }}" width="50px" height="50px" alt="product image" style="object-fit: cover; border-radius: 10px;">
                                </td>
                                <td style="padding: 8px;">{{ $product['product_name'] }}</td>
                                <td style="padding: 8px;">{{ $product['purchase_quantity'] }}</td>
                                <td style="padding: 8px;">₦{{ $product['purchase_price'] }}</td>
                                <td style="padding: 8px;">₦{{ $product['purchase_price'] * $product['purchase_quantity'] }}</td>
                            </tr>
                            @php
                                $subTotal += $product['regular_price'] * $product['purchase_quantity'];
                                $total += $product['purchase_total_price'];
                            @endphp
                        @endforeach
                        @php
                            $saveTotal = $subTotal - $total;
                        @endphp
                    </tbody>
                    <tfoot>
                       <tr>
                            <td colspan="4" style="padding: 8px; text-align: right;"><strong>Sub Total</strong></td>
                            <td style="padding: 8px; text-align: right;"><strong>₦{{ number_format($subTotal, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 8px; text-align: right;"><strong>Total Save</strong></td>
                            <td style="padding: 8px; text-align: right;"><strong>₦{{ number_format($saveTotal, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 8px; text-align: right;"><strong>Discount</strong></td>
                            <td style="padding: 8px; text-align: right;"><strong>₦{{ number_format($order->coupon_discount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 8px; text-align: right;"><strong>Total Pay</strong></td>
                            <td style="padding: 8px; text-align: right;"><strong>₦{{ number_format($order->net_amount, 2) }}</strong></td>
                        </tr>

                    </tfoot>
                </table>
            </div>

            <div class="shipping-info">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 50%;">
                           <p><strong>Shipping Address:</strong></p>
                            @php
                                $address = json_decode($order->shiping_address_id, true);
                            @endphp
                            <p>
                                {{ $address['name'] }}<br>
                                {{ $address['address'] }}<br>
                                {{ $address['city'] }}, {{ $address['state'] }}<br>
                                {{ $address['country'] }} - {{ $address['zip_code'] }}
                            </p>
                        </td>
                        @if($order->delivery_option == 'Pickup' && $order->warehouse_id != null)
                            <td style="width: 50%; text-align: right;">
                                <p><strong>WareHouse Address:</strong></p>
                                @php
                                    $warehouse = json_decode($order->warehouse_id, true);
                                @endphp
                                <p>
                                    {{ $warehouse['warehouse_name'] }}<br>
                                    {{ $warehouse['street_address'] }}<br>
                                    {{ $warehouse['city'] }}, {{ $warehouse['state'] }}<br>
                                    {{ $warehouse['country'] }} - {{ $warehouse['zip_code'] }}
                                </p>
                                <p><strong>WareHouse Contact Details:</strong></p>
                                <p>
                                    {{ $warehouse['contact_name'] }}<br>
                                    {{ $warehouse['contact_email'] }}<br>
                                    {{ $warehouse['country_code'] }} {{ $warehouse['contact_number'] }}<br>
                                </p>
                            </td>
                        @endif
                    </tr>
                </table>
            </div>

            <div class="inventory-info">
                <h3>Inventory Status</h3>
                <p><strong>Check if the stock levels need replenishment for any products in this order.</strong></p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NEXA. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
