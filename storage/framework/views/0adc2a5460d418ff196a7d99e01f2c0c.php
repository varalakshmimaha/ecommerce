<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 5px 5px;
        }
        .credentials {
            background-color: white;
            padding: 20px;
            border-left: 4px solid #4F46E5;
            margin: 20px 0;
        }
        .credential-item {
            margin: 10px 0;
        }
        .credential-label {
            font-weight: bold;
            color: #4F46E5;
        }
        .credential-value {
            font-family: 'Courier New', monospace;
            background-color: #f3f4f6;
            padding: 5px 10px;
            border-radius: 3px;
            display: inline-block;
            margin-top: 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Welcome to Our Store!</h1>
    </div>

    <div class="content">
        <h2>Hello <?php echo e($userName); ?>!</h2>

        <p>Thank you for placing your order <strong>#<?php echo e($orderNumber); ?></strong>. We've created an account for you to track your orders and enjoy a better shopping experience.</p>

        <div class="credentials">
            <h3 style="margin-top: 0;">Your Login Credentials</h3>

            <div class="credential-item">
                <div class="credential-label">Email:</div>
                <div class="credential-value"><?php echo e($userEmail); ?></div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Password:</div>
                <div class="credential-value"><?php echo e($userPassword); ?></div>
            </div>
        </div>

        <p><strong>Important:</strong> For your security, we recommend changing your password after your first login.</p>

        <center>
            <a href="<?php echo e(url('/user/login')); ?>" class="button">Login to Your Account</a>
        </center>

        <h3>What You Can Do:</h3>
        <ul>
            <li>Track your current and past orders</li>
            <li>Save your shipping addresses</li>
            <li>Manage your profile</li>
            <li>Get faster checkout</li>
        </ul>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <p>Happy Shopping!<br>
        <strong>The Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; <?php echo e(date('Y')); ?> All rights reserved.</p>
    </div>
</body>
</html>
<?php /**PATH /home/bestprime-ecommerce/htdocs/ecommerce.bestprime.live/suvee/resources/views/emails/new-user-credentials.blade.php ENDPATH**/ ?>