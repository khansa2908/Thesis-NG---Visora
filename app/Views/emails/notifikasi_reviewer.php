<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Penugasan Reviewer</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
        <h2 style="color: #4e73df; text-align: center;">Penugasan Reviewer Sempro</h2>
        <hr>
        <p>Yth. Bapak/Ibu <strong><?= $nama_dosen ?></strong>,</p>
        <p>Anda telah ditugaskan sebagai <b>Reviewer Seminar Proposal</b> untuk mahasiswa berikut:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee; width: 30%;"><strong>Mahasiswa</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">: <?= $mahasiswa ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Judul</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">: <?= $judul ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Tanggal</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">: <?= date('d-m-Y', strtotime($tanggal)) ?></td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Waktu</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">: <?= $waktu ?> WIB</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>Ruang/Link</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #eee;">: <?= $ruang ?></td>
            </tr>
        </table>

        <p style="margin-top: 20px;">Mohon kehadirannya tepat waktu. Terima kasih.</p>
        <br>
        <p style="font-size: 11px; color: #777; text-align: center;">Email ini dikirim secara otomatis oleh Sistem Informasi Skripsi.</p>
    </div>
</body>
</html>