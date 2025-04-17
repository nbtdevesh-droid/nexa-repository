<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Forget Password</title>
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
            text-align: center;
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
        <p>Hello!</p>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <p class="otp"><a href="{{ route('password.reset', $token) }}">Reset Password</a></p>
        <p>This password reset link will expire in 60 minutes.</p>
        <p>If you did not request a password reset, no further action is required.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} NEXA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>