<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cancellation Request</title>
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
            <h2>Order Cancellation Request</h2>
            <p>Dear Admin,</p>

            <p>The order has been cancelled. Below are the details:</p>
            <h3>Order Details:</h3>
            <ul>
                <li><strong>Order ID:</strong> {{ $order->order_id }}</li>
                <li><strong>Customer Name:</strong> {{ $user }}</li>
                <li><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</li>
                <li><strong>Total Amount:</strong> ₦{{ $order->net_amount }}</li>
            </ul>
            {{-- @if($bank_detail != "")
                <h3>Bank Details:</h3>
                <ul>
                    <li><strong>Bank Name:</strong> {{ $bank_detail->bank_name }}</li>
                    <li><strong>Bank Address:</strong> {{ $bank_detail->bank_address }}</li>
                    <li><strong>Country:</strong> {{ $bank_detail->country }}</li>
                    <li><strong>Account Holder Name:</strong> {{ $bank_detail->account_holder_name }}</li>
                    <li><strong>IFSC Code:</strong> ${{ $bank_detail->ifsc_code }}</li>
                    <li><strong>Account Number:</strong> ${{ $bank_detail->account_number }}</li>
                </ul>
            @endif --}}

            <p>Please review the order and take any necessary actions.</p>

            <p>Best regards,<br>
            <strong>NEXA</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NEXA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
