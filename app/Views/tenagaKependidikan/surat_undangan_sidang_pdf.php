<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Undangan Sidang</title>
    <style>
        /* MARGIN DIPERKECIL AGAR MUAT 1 HALAMAN */
        @page {
            margin: 1.5cm 2.5cm 1cm 2.5cm;
            /* Top Right Bottom Left */
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.2;
            color: #000;
        }

        /* HEADER - Pastikan Gambar Full */
        .header {
            margin-bottom: 10px;
        }

        .header img {
            height: 85px;
            /* Tinggi disesuaikan */
            width: auto;
        }

        /* INFO SURAT (Rujukan) - BOLD LABEL */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .info-table td {
            vertical-align: top;
            padding: 1px 0;
        }

        .lbl-info {
            width: 110px;
        }

        .sep-info {
            width: 15px;
            text-align: center;
        }

        /* LIST KEPADA YTH */
        .yth-container {
            margin-bottom: 15px;
        }

        .yth-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .yth-table td {
            vertical-align: top;
            padding: 1px 0;
        }

        .col-no {
            width: 20px;
        }

        .col-nama {
            width: 280px;
        }

        /* Nama Dosen REGULAR sesuai contoh */
        .col-jabatan {}

        /* Jabatan REGULAR sesuai contoh */

        .content {
            text-align: justify;
            line-height: 1.3;
        }

        /* TABEL DATA (Mahasiswa & Jadwal) - REGULAR TEXT */
        .tabel-data {
            width: 100%;
            margin-left: 30px;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .tabel-data td {
            vertical-align: top;
            padding: 2px 0;
        }

        .col-lbl {
            width: 110px;
        }

        /* Label Regular */
        .col-sep {
            width: 15px;
            text-align: center;
        }

        .col-val {}

        /* Isi Regular */

        /* TANDA TANGAN */
        .ttd-wrapper {
            margin-top: 20px;
            width: 100%;
            position: relative;
            height: 120px;
        }

        .ttd-box {
            float: right;
            width: 45%;
            text-align: left;
        }

        .ttd-jabatan {
            margin-bottom: 60px;
            line-height: 1.2;
        }

        .ttd-nama {
            font-weight: bold;
        }

        /* TEMBUSAN */
        .tembusan {
            margin-top: 5px;
            font-size: 10pt;
        }

        .tembusan ol {
            margin: 0;
            padding-left: 18px;
        }

        /* FOOTER */
        .page-footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            line-height: 1.2;
        }

        .smart-campus {
            color: #00913f;
            font-weight: bold;
            font-style: italic;
            font-size: 10pt;
            margin-bottom: 2px;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="<?= $logo_src ?>" alt="Kop Surat">
    </div>

    <table class="info-table">
        <tr>
            <td class="lbl-info">Rujukan kami</td>
            <td class="sep-info">:</td>
            <td><?= $no_surat ?></td>
        </tr>
        <tr>
            <td class="lbl-info">Rujukan anda</td>
            <td class="sep-info">:</td>
            <td></td>
        </tr>
        <tr>
            <td class="lbl-info">Perihal</td>
            <td class="sep-info">:</td>
            <td>Undangan Ujian Skripsi</td>
        </tr>
    </table>

    <div class="yth-container">
        <div>Kepada Yth</div>
        <table class="yth-table">
            <tr>
                <td class="col-no"></td>
                <td class="col-nama"><?= $data['ketua_penguji'] ?></td>
                <td class="col-jabatan">Ketua Penguji</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-nama"><?= $data['anggota_penguji'] ?></td>
                <td class="col-jabatan">Anggota Penguji</td>
            </tr>
            <tr>
                <td class="col-no"></td>
                <td class="col-nama"><?= $data['pembimbing1'] ?></td>
                <td class="col-jabatan">Pembimbing Ilmu / Penguji</td>
            </tr>
            <?php if (!empty($data['pembimbing2']) && $data['pembimbing2'] != '-'): ?>
                <tr>
                    <td class="col-no"></td>
                    <td class="col-nama"><?= $data['pembimbing2'] ?></td>
                    <td class="col-jabatan">Pembimbing Ilmu 2 / Penguji</td>
                </tr>
            <?php endif; ?>

            <?php if (!empty($data['nama_klien'])): ?>
                <tr>
                    <td class="col-no"></td>
                    <td class="col-nama"><?= $data['nama_klien'] ?></td>
                    <td class="col-jabatan">Klien</td>
                </tr>
            <?php endif; ?>

            <tr>
                <td class="col-no"></td>
                <td class="col-nama"><?= $data['pembimbing_agama'] ?></td>
                <td class="col-jabatan">Pembimbing Agama / Penguji</td>
            </tr>
        </table>

        <div style="margin-top: 10px;">di</div>
        <div style="margin-left: 0px;">Universitas YARSI</div>
    </div>

    <div class="content">
        <p style="margin-bottom: 10px;">Assalamu'alaikum Wr. Wb.</p>

        <p style="margin-bottom: 10px;">Sehubungan dengan penyelesaian studi mahasiswa Program Studi Teknik Informatika Program Sarjana Fakultas Teknologi Informasi Universitas YARSI:</p>

        <table class="tabel-data">
            <tr>
                <td class="col-lbl">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $data['nama_mhs'] ?></td>
            </tr>
            <tr>
                <td class="col-lbl">NPM</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $data['npm'] ?></td>
            </tr>
            <tr>
                <td class="col-lbl">Program Studi</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $data['nama_prodi'] ?></td>
            </tr>
            <tr>
                <td class="col-lbl">Judul Skripsi</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $data['judul'] ?></td>
            </tr>
        </table>

        <p style="margin-bottom: 10px;">Kami mohon kesediaan Bapak/Ibu untuk hadir sebagai Pembimbing sekaligus Penguji dalam ujian skripsi yang Insya Allah akan dilakukan secara daring pada:</p>

        <table class="tabel-data">
            <tr>
                <td class="col-lbl">Hari / tanggal</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $hari_sidang ?>, <?= $tgl_sidang ?></td>
            </tr>
            <tr>
                <td class="col-lbl">Waktu</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $jam_mulai ?> WIB</td>
            </tr>
            <tr>
                <td class="col-lbl">Tempat</td>
                <td class="col-sep">:</td>
                <td class="col-val"><?= $data['ruang'] ?></td>
            </tr>
        </table>

        <p>Demikian, atas perhatian Bapak/Ibu kami mengucapkan terimakasih.</p>

        <p>Wassalamu'alaikum Wr. Wb.</p>
    </div>

    <div class="ttd-wrapper">
        <div class="ttd-box">
            <div class="ttd-date">Jakarta, <?= $tanggal_surat ?></div>
            <div class="ttd-jabatan">
                <?= $jabatan_kaprodi ?><br>
                Program Sarjana
            </div>
            <div class="ttd-nama"><?= $kaprodi ?></div>
        </div>
    </div>

    <div class="tembusan">
        <div>Tembusan Yth.</div>
        <ol>
            <li>Dekan FTI Universitas YARSI</li>
            <li>Arsip</li>
        </ol>
    </div>

    <div class="page-footer">
        <div class="smart-campus">Smart Campus That You Can Rely On</div>
        <div>Jl. Letjend. Suprapto, Cempaka Putih, Jakarta 10510</div>
        <div>Telp./Fax. 62.21-4269301 atau 62.21-4206674 (Hunting) Ext. 5005</div>
        <div>www.yarsi.ac.id</div>
    </div>

</body>

</html>