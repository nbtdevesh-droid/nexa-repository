<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
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
        .otp {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            color: #007bff;
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
            <h2>Your account has been created by the admin.</h2>
            <p>Dear <strong>{{$data->first_name . ' ' . $data->last_name}}</strong>,</p>

            <p>Welcome to <strong>NEXA</strong>! We are delighted to have you as a valued customer. Our goal is to provide you with the best grocery shopping experience, offering a wide range of high-quality products at competitive prices.</p>

            <p><strong>Register Details:</strong><br>
            Name: {{$data->first_name . ' ' . $data->last_name}}<br>
            Phone Number: {{$data->phone}}<br>
            Email: {{$data->email}}<br>
            Account Status: Active</p>

            <p>Thank you for choosing <strong>NEXA</strong>. We look forward to serving you and providing you with a delightful shopping experience.</p>

            <p><strong>Best regards,<br>NEXA</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NEXA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
