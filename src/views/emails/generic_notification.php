<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><?= htmlspecialchars($title) ?></div>
        <div class="content">
            <p>Halo,</p>
            <p><?= htmlspecialchars($message) ?></p>
            <?php if (!empty($link)): ?>
                <a href="<?= htmlspecialchars($link) ?>" class="button">Lihat Detail</a>
            <?php endif; ?>
        </div>
        <div class="footer">
            <p>Ini adalah email yang dibuat secara otomatis. Mohon tidak membalas email ini.</p>
            <p>&copy; <?= date('Y') ?> DocuTrack. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
