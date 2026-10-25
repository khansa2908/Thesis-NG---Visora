<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <div style="font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.6; padding: 40px; border: 1px solid #000; max-width: 700px; margin: 20px auto; background-color: #fff;">
        
        <div style="text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 25px;">
            <h2 style="margin: 0; text-transform: uppercase; font-size: 18px; letter-spacing: 1px;">
                <?= $title ?>
            </h2>
            <p style="margin: 5px 0 0 0; font-size: 14px;">UNIVERSITAS YARSI</p>
        </div>

        <div style="margin: 0 20px;">
            <p style="margin-bottom: 20px;">
                Kepada Yth.<br>
                <strong>Bapak/Ibu <?= $nama_dosen ?></strong><br>
                di Tempat
            </p>

            <p style="text-align: justify; text-indent: 40px; margin-bottom: 20px;">
                Program Studi <?= $nama_prodi ?> Universitas YARSI akan mengadakan <strong><?= $nama_kegiatan ?></strong> bagi mahasiswa yang bersangkutan. Sehubungan dengan hal tersebut, kami mengundang Bapak/Ibu untuk dapat hadir dan berpartisipasi sebagai <strong><?= $selaku ?></strong> pada:
            </p>

            <table style="margin: 20px auto; width: 90%; border-collapse: collapse; font-size: 15px;">
                <tr>
                    <td style="padding: 5px; font-weight: bold;" width="30%">Mahasiswa</td>
                    <td style="padding: 5px;" width="5%">:</td>
                    <td style="padding: 5px;"><?= $nama_mhs ?> (<?= $npm_mhs ?>)</td>
                </tr>
                <tr>
                    <td style="padding: 5px; font-weight: bold;">Judul</td>
                    <td style="padding: 5px;">:</td>
                    <td style="padding: 5px;"><em>"<?= $judul_skripsi ?>"</em></td>
                </tr>
                <tr>
                    <td style="padding: 5px; font-weight: bold;">Tanggal</td>
                    <td style="padding: 5px;">:</td>
                    <td style="padding: 5px;"><?= date("d F Y", strtotime((string)$tanggal)) ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px; font-weight: bold;">Jam</td>
                    <td style="padding: 5px;">:</td>
                    <td style="padding: 5px;"><?= $jam ?> WIB</td>
                </tr>
                <tr>
                    <td style="padding: 5px; font-weight: bold;">Tempat / Tautan</td>
                    <td style="padding: 5px;">:</td>
                    <td style="padding: 5px;">
                        <?= (!empty($link_seminar)) ? '<a href="'.$link_seminar.'" style="color: blue; text-decoration: underline;">Klik Tautan Daring</a>' : $ruangan; ?>
                    </td>
                </tr>
            </table>

            <p style="text-indent: 40px; margin-top: 25px;">
                Demikian surat ini disampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.
            </p>
        </div>

        <div style="text-align: right; margin-top: 60px; padding-right: 40px;">
            Jakarta, <?= date("d F Y") ?><br>
            <p style="margin-top: 60px; font-weight: bold; text-decoration: underline;">
                Kaprodi <?= $inisial_prodi ?>
            </p>
        </div>
    </div>
</body>
</html>