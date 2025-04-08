<!DOCTYPE html>
<html>
<head>
    <title>Database Backup</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { padding: 20px; }
        .details { margin-top: 20px; background: #f9f9f9; padding: 15px; border-radius: 6px; }
        .details p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📦 Database Backup Successful</h2>
        <p>The latest database backup has been completed and is attached to this email.</p>

        <div class="details">
            <p><strong>📅 Date:</strong> {{ $backupDate }}</p>
            <p><strong>📄 File Name:</strong> {{ $fileName }}</p>
            <p><strong>📏 File Size:</strong> {{ $fileSize }}</p>
        </div>

        <p>If you did not request this backup, please review your security settings.</p>

        <p style="margin-top: 30px;">— Your System</p>
    </div>
</body>
</html>
