<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Refund Has Been Processed Successfully {{ $order->order_id }}</title>
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
            <p>Dear {{ $user }},</p>
        
            <p>We wanted to let you know that we have successfully processed a refund for your recent order. We apologize for any inconvenience this may have caused and appreciate your patience during the process.</p>
            
            <h3>Refund Details</h3>
            <ul>
                <li><strong>Order ID:</strong> {{ $order->order_id }}</li>
                <li><strong>Status:</strong> Refunded</li>
                <li><strong>Refund Amount:</strong> ₦{{ $order->net_amount ?? 'Amount not specified' }}</li>
            </ul>

            <div class="inventory-info">
                <p>Please allow 7-12 business days for the refund to appear in your account, depending on your bank’s processing time.</p>
                <p>Thank you for choosing us. We look forward to serving you again in the future.</p>
                <p>Best regards,<br><strong>NEXA</strong></p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NEXA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
