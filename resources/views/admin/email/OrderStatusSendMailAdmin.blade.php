<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update for Order {{ $order->order_id }}</title>
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
            <p>Dear,</p>
            @if($order->order_status == 'confirm')
                <p>A new order has been confirmed and is awaiting your attention.</p>
            @elseif($order->order_status == 'processing')
                <p>The order has moved to the "Processing" stage. Please review the details below.</p>
            @elseif($order->order_status == 'dispatch')
                <p>The order has been successfully dispatched. Please find the details below:</p>
            @elseif($order->order_status == 'delivered')
                <p>The order has been successfully delivered to the customer. Please find the details below:</p>
            @elseif($order->order_status == 'complete')
                <p>The order has been successfully completed. Here are the final details:</p>
            @elseif($order->order_status == 'cancelled')
                <p>The order has been cancelled. Below are the details:</p>
            @elseif($order->order_status == 'return')
                <p>The order has been returned by the customer. Please find the details below:</p>
            @endif
            
            <h3>Order Details</h3>
            <ul>
                <li><strong>Order Number:</strong> {{ $order->order_id }}</li>
                <li><strong>Customer Name:</strong> {{ ucfirst($user) }}</li>
                <li><strong>Order Status:</strong> {{ $order->order_status }}</li>
                @if($order->order_status != 'delivered')
                    <li><strong>Order Date:</strong>  @if($order->created_at) {{ $order->created_at->format('d F, Y') }} @else Not available @endif</li>
                    <li><strong>Estimated Delivery Date:</strong> @if($order->shiping_date) {{ $order->shiping_date->format('d F, Y') }} @else Not available @endif</li>
                @else
                    <li><strong>Delivered On:</strong> @if($order->shiping_date) {{ $order->shiping_date->format('d F, Y') }} @else Not available @endif</li>
                @endif
                <li><strong>Total Amount:</strong> ₦{{ $order->net_amount }}</li>
            </ul>

            <div class="inventory-info">
                @if($order->order_status == 'confirm')
                    <p><strong>Please log in to the admin panel to view the full order details and proceed with further actions.</strong></p>
                @elseif($order->order_status == 'processing')
                    <p><strong>Please log in to the admin panel to track the status and manage the order further.</strong></p>
                @elseif($order->order_status == 'dispatch')
                    <p><strong>The order is now on its way to the customer. You can track its progress from the admin panel.</strong></p>
                @elseif($order->order_status == 'delivered')
                    <p><strong>The customer has received the order. You can now mark it as completed or take any further actions from the admin panel.</strong></p>
                @elseif($order->order_status == 'complete')
                    <p><strong>The order is now fully processed and completed. You may review the order details or close it in the admin panel.</strong></p>
                @elseif($order->order_status == 'cancelled')
                    <p><strong>Please review the order and take any necessary actions.</strong></p>
                @elseif($order->order_status == 'return')
                    <p><strong>Please review the return and process accordingly.</strong></p>
                @endif
                <p>Best regards,<br><strong>NEXA</strong></p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NEXA. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
