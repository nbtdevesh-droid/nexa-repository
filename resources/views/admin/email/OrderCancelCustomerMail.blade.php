<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cancellation Successful</title>
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
            line-height: 1.6;
        }
        .content .order-details {
            margin: 20px 0;
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
            <h2>Order Cancellation Successful</h2>
            <p>Dear {{ $user->first_name . ' ' . $user->last_name }},</p>

            <p>We are writing to confirm that your order <strong>{{ $order->order_id }}</strong>, placed on <strong>{{ $order->created_at->format('M d, Y') }}</strong>, has been successfully <strong>cancelled</strong> at your request.</p>

            <h3>Order Details:</h3>
            <ul>
                <li><strong>Order ID:</strong> {{ $order->order_id }}</li>
                <li><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</li>
                <li><strong>Cancellation Date:</strong> {{ now()->format('M d, Y') }}</li>
                <li><strong>Total Amount:</strong> ₦{{ $order->net_amount }}</li>
            </ul>

            @if ($order->payment_mode != 'cod')
                <h3>Refund Information:</h3>
                <p>If you made a payment, the refund process has already started and will be completed within <strong>7 working days</strong>.</p>
            @endif
            <p>Thank you for shopping with us. We hope to serve you again in the future.</p>

            <p>Best regards,<br>
            <strong>NEXA</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NEXA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
