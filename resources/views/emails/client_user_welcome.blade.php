<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Portal Drive</title>
</head>
<body>
    <h2>Hi {{ $clientUser->name }} {{ $clientUser->surname }},</h2>
    <p>You can access Portal Drive!</p>
    <p>Your login details are:</p>
    <ul>
        <li><strong>Email:</strong> {{ $clientUser->email }}</li>
        <li><strong>Password:</strong> {{ $plainPassword }}</li>
    </ul>
    <p>You can log in at: <a href="https://mishor-portal-user.vercel.app">Portal Drive Login</a></p>
    <br>
    <p>Best regards,</p>
    <p>Mishor Team</p>
</body>
</html>
