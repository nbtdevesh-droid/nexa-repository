<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if($order->order_status == 'confirm')
           Your Order Confirmation -  {{ $order->order_id }}
        @elseif($order->order_status == 'processing')
            Your Order is Being Processed - {{ $order->order_id }}
        @elseif($order->order_status == 'dispatch')
            Your Order Has Been Dispatched - {{ $order->order_id }}
        @elseif($order->order_status == 'delivered')
            Your Order Has Been Delivered - {{ $order->order_id }}
        @elseif($order->order_status == 'complete')
            Your Order Has Been Completed - {{ $order->order_id }}
        @elseif($order->order_status == 'cancelled')
            Your Order Has Been Cancelled - {{ $order->order_id }}
        @elseif($order->order_status == 'return')
            Your Order Has Been Returned - {{ $order->order_id }}
        @endif</title>
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
            <p>Hi {{ $user->first_name . ' ' . $user->last_name }},</p>
            @if($order->order_status == 'confirm')
                <p>Thank you for shopping with us! We're happy to confirm that your order {{ $order->order_id }} has been successfully placed.</p>
            @elseif($order->order_status == 'processing')
                <p>Thank you for your order! We are excited to inform you that your order {{ $order->order_id }} is currently being processed and will soon be on its way.</p>
            @elseif($order->order_status == 'dispatch')
                <p>Great news! Your order {{ $order->order_id }} has been dispatched and is on its way to you.</p>
            @elseif($order->order_status == 'delivered')
                <p>We are happy to inform you that your order {{ $order->order_id }} has been successfully delivered!</p>
            @elseif($order->order_status == 'complete')
                <p>We are happy to inform you that your order {{ $order->order_id }} has been completed successfully! 🎉</p>
            @elseif($order->order_status == 'cancelled')
                <p>We regret to inform you that your recent order {{ $order->order_id }} has been canceled by our team. We understand that this might be disappointing, and we sincerely apologize for any inconvenience this may have caused.</p>
            @elseif($order->order_status == 'return')
                <p>We have successfully received your return request for Order {{ $orders->order_id }}</p>
            @endif
            
            <h3>Order Details</h3>
            <ul>
                <li><strong>Order Number:</strong> {{ $order->order_id }}</li>
                @if($order->order_status != 'delivered')
                    <li><strong>Order Date:</strong>  @if($order->created_at) {{ $order->created_at->format('d F, Y') }} @else Not available @endif</li>
                    <li><strong>Estimated Delivery Date:</strong> @if($order->shiping_date) {{ $order->shiping_date->format('d F, Y') }} @else Not available @endif</li>
                @else
                    <li><strong>Delivered On:</strong> @if($order->shiping_date) {{ $order->shiping_date->format('d F, Y') }} @else Not available @endif</li>
                @endif
            </ul>

            <div class="order-summary">
                @if($order->order_status == 'confirm')
                    <h3>Items Ordered:</h3>
                @elseif($order->order_status == 'processing')
                    <h3>Items Ordered:</h3>
                @elseif($order->order_status == 'dispatch')
                    <h3>Items Shipped:</h3>
                @elseif($order->order_status == 'delivered')
                    <h3>Items Delivered:</h3>
                @endif
                
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 8px;">Image</th>
                            <th style="padding: 8px;">Product Name</th>
                            <th style="padding: 8px;">Quantity</th>
                            <th style="padding: 8px;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(json_decode($order->product_complete_details, true) as $product)
                            <tr>
                                <td style="padding: 8px;">
                                    <img src="{{ asset('admin-assets/assets/img/product/feature_img/' . $product['feature_image']) }}" width="50px" height="50px" style="border-radius: 10px;" alt="product image">
                                </td>
                                <td style="padding: 8px;">{{ $product['product_name'] }}</td>
                                <td style="padding: 8px;">{{ $product['purchase_quantity'] }}</td>
                                <td style="padding: 8px;">${{ $product['purchase_price'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- <div class="shipping-info">
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
            </div> --}}

            <div class="inventory-info">
                @if($order->order_status == 'confirm')
                    <p><strong>Thank you for choosing NEXA. We look forward to serving you again soon!</strong></p>
                @elseif($order->order_status == 'processing')
                    <p><strong>Thank you for shopping with NEXA!</strong></p>
                @elseif($order->order_status == 'dispatch')
                    <p><strong>Thank you for choosing NEXA. We hope you enjoy your purchase!</strong></p>
                @elseif($order->order_status == 'delivered')
                    <p><strong>Your feedback is important to us! Please consider sharing your experience or leaving a review to help us serve you better in the future. <br>
                        Thank you for shopping with NEXA. We look forward to serving you again!</strong></p>
                @elseif($order->order_status == 'complete')
                    <p><strong>Thank you for shopping with NEXA! We look forward to serving you again.</strong></p>
                @elseif($order->order_status == 'cancelled')
                    <p><strong>Once again, we apologize for any inconvenience caused, and we appreciate your understanding. We hope to serve you again soon.</strong></p>
                    @if ($order->payment_mode != 'cod')
                        <h3>Refund Information:</h3>
                        <p>If you made a payment, the refund process has already started and will be completed within <strong>7 working days</strong>.</p>
                    @endif
                @elseif($order->order_status == 'return')
                   
                @endif
                <p>Best regards,<br><strong>NEXA Team</strong></p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NEXA. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
