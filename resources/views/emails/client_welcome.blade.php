<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Portal Drive</title>
</head>
<body>
    <h2>Hi {{ $client->name }} {{ $client->surname }},</h2>
    <p>Thank you for registering on Portal Drive!</p>
    <p>Your login details are:</p>
    <ul>
        <li><strong>Email:</strong> {{ $client->email }}</li>
        <li><strong>Password:</strong> {{ $plainPassword }}</li>
    </ul>
    <p>You can log in at: <a href="https://mishor-portal-client.vercel.app">Portal Drive Login</a></p>
    <p>If you did not register, please contact us immediately.</p>
    <br>
    <p>Best regards,</p>
    <p>Mishor Team</p>
</body>
</html>
