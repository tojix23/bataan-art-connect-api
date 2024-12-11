<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Rejection Notice</title>
</head>

<body>
    <h1>Hello, {{ $user }}</h1>

    <p>We regret to inform you that your account verification has been <strong>rejected</strong>.</p>

    <p><strong>Reason for Rejection:</strong></p>
    <p>{{ $reason }}</p>

    <p>We encourage you to review the details and ensure that all information provided meets our verification
        requirements. If you believe this decision was made in error, please feel free to reach out to our support team
        for clarification.</p>

    <p>Thank you for your understanding.</p>

    <p>Best regards,<br>The Bataan Art Team</p>
</body>

</html>
