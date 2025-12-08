<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { background: #0A4A7F; color: #fff; padding: 15px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #0A4A7F; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 4px; color: #fff; font-weight: bold; font-size: 12px; }
        .bg-green { background-color: #28a745; }
        .bg-red { background-color: #dc3545; }
        .bg-blue { background-color: #007bff; }
        .bg-yellow { background-color: #ffc107; color: #000; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        th { width: 30%; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>DocuTrack Notification</h2>
        </div>
        <div class="content">
            <p>Halo <strong><?= htmlspecialchars($nama_penerima) ?></strong>,</p>
            
            <p><?= htmlspecialchars($pesan_pembuka) ?></p>

            <div style="text-align: center; margin: 20px 0;">
                <span class="status-badge <?= $status_color_class ?>">
                    <?= htmlspecialchars($status_label) ?>
                </span>
            </div>

            <?php if (!empty($detail_kegiatan)) : ?>
            <h3>Detail Kegiatan:</h3>
            <table>
                <tr>
                    <th>Nama Kegiatan</th>
                    <td><?= htmlspecialchars($detail_kegiatan['namaKegiatan']) ?></td>
                </tr>
                <tr>
                    <th>Pengusul</th>
                    <td><?= htmlspecialchars($detail_kegiatan['pemilikKegiatan']) ?></td>
                </tr>
                <tr>
                    <th>Tanggal Pengajuan</th>
                    <td><?= htmlspecialchars(date('d F Y', strtotime($detail_kegiatan['createdAt']))) ?></td>
                </tr>
                <?php if (!empty($catatan_tambahan)) : ?>
                <tr>
                    <th>Catatan/Alasan</th>
                    <td style="color: #d9534f; font-weight: bold;"><?= nl2br(htmlspecialchars($catatan_tambahan)) ?></td>
                </tr>
                <?php endif; ?>
            </table>
            <?php endif; ?>

            <div style="text-align: center;">
                <a href="<?= $link_action ?>" class="btn">Lihat di Aplikasi</a>
            </div>
        </div>
        <div class="footer">
            <p>&copy; <?= date('Y') ?> DocuTrack System. Politeknik Negeri Jakarta.</p>
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
