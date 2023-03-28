<head>
    <title>QR Code for Login</title>
</head>
<body>
    <h1>QR Code for Login</h1>
    <p>Use the following QR code to log in to the Coffee App: </p>
    <img src="{{ $message->embedData($qrCode, 'qr-code.png', 'image/png') }}">
    <p>This QR code is only valid for one use and will expire after a short period of time. If you have any problems logging in, please contact our support team.</p>
</body>
</html>