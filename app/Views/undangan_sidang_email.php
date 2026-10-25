<!DOCTYPE html>
<html>
<body style="margin: 0; padding: 0; background-color: #f4f4f4;">
    <div style="font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.6; padding: 40px; border: 1px solid #000; max-width: 700px; margin: 20px auto; background-color: #fff;">
        
        <div style="text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 25px;">
            <h2 style="margin: 0; text-transform: uppercase; font-size: 18px;"><?= $title ?></h2>
            <p style="margin: 5px 0 0 0; font-size: 14px;">Program Studi <?= $nama_prodi ?></p>
        </div>

        <div style="margin: 0 20px;">
            <p>Kepada Yth.<br><strong>Bapak/Ibu <?= $nama_dosen ?></strong><br>di Tempat</p>

            <p style="text-align: justify;">
                Dengan hormat, sehubungan dengan pelaksanaan Sidang Akhir Skripsi, kami menginformasikan penugasan Bapak/Ibu sebagai <strong><?= $selaku ?></strong> untuk mahasiswa berikut:
            </p>

            <table style="margin: 20px 0; width: 100%; border-collapse: collapse; font-size: 15px;">
                <tr><td width="30%"><strong>Mahasiswa</strong></td><td width="5%">:</td><td><?= $nama_mhs ?> (<?= $npm_mhs ?>)</td></tr>
                <tr><td><strong>Judul Skripsi</strong></td><td>:</td><td><em>"<?= $judul_skripsi ?>"</em></td></tr>
                <tr><td><strong>Hari/Tanggal</strong></td><td>:</td><td><?= $tanggal ?></td></tr>
                <tr><td><strong>Waktu</strong></td><td>:</td><td><?= $jam ?> WIB</td></tr>
                <tr><td><strong>Ruangan</strong></td><td>:</td><td><?= $ruangan ?></td></tr>
            </table>

            <p>Demikian informasi ini kami sampaikan agar dapat dipersiapkan dengan baik. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.</p>
        </div>

        <div style="text-align: right; margin-top: 50px; padding-right: 40px;">
            Jakarta, <?= date("d F Y") ?><br><br><br><br>
            <strong>Kaprodi <?= $inisial_prodi ?></strong>
        </div>
    </div>
</body>
</html>