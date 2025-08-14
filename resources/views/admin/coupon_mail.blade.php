
    {{-- <h1>Coupon Details!</h1>
    <p>Your Coupon Code is {{ $send_details['coupon_code'] }}.</p>
    <p>Your Coupon Title is {{ $send_details['coupon_title'] }}.</p>
    <p>Your Coupon Amount is {{ $send_details['amount'] }}.</p> --}}


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupon Details</title>
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
            <img src="{{ asset('/admin-assets/dist/img/logo_dashboard.png') }}" alt="logo_dashboard"
                class="brand-image img-circle elevation-3">
        </div>
        <div class="content">
            <h2>Coupon Details</h2>
            <p>A new Coupon has been placed on <strong>NEXA</strong>. Here are the details:</p>
            <p>Thank you for being a valued customer. We are excited to share an exclusive coupon code just for you!</p>
            <ul>
                <li><strong>Coupon Code:</strong> {{  $send_details['coupon_code'] }}</li>
                <li><strong>Coupon Name:</strong> {{ $send_details['coupon_title'] }}</li>
                <li><strong>Coupon Amount:</strong> $ {{ $send_details['amount'] }}</li>
                
            </ul>
            <p>Use this code at checkout to enjoy a special discount on your next purchase!</p>
            <p>Thank you for shopping with us!</p>
            
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NEXA. All rights reserved.</p>
        </div>
    </div>
</body>

</html>